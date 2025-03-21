import os
import requests
import zipfile
import pandas as pd
import json
from tqdm import tqdm

def baixar_e_descompactar(ano):
    url = f"https://cdn.tse.jus.br/estatistica/sead/odsele/votacao_candidato_munzona/votacao_candidato_munzona_{ano}.zip"
    zip_path = f"votacao_candidato_munzona_{ano}.zip"
    extract_path = os.path.join("zip", str(ano))
    
    # Verifica se o arquivo já foi baixado e descompactado
    if os.path.exists(extract_path):
        print(f"Arquivos para o ano {ano} já existem. Pulando download e descompactação.")
        return
    
    try:
        # Faz o download do arquivo com barra de progresso
        response = requests.get(url, stream=True)
        response.raise_for_status()
        total_size = int(response.headers.get("content-length", 0))
        
        with open(zip_path, "wb") as f, tqdm(
            desc=f"Baixando {ano}",
            total=total_size,
            unit="B",
            unit_scale=True,
            unit_divisor=1024,
        ) as bar:
            for chunk in response.iter_content(chunk_size=1024):
                if chunk:
                    f.write(chunk)
                    bar.update(len(chunk))
        print(f"\nDownload concluído: {zip_path}")
        
        # Cria a pasta do ano se não existir
        os.makedirs(extract_path, exist_ok=True)
        
        # Descompacta o arquivo ZIP com barra de progresso
        with zipfile.ZipFile(zip_path, "r") as zip_ref:
            file_list = zip_ref.namelist()
            with tqdm(total=len(file_list), desc=f"Extraindo {ano}") as bar:
                for file in file_list:
                    zip_ref.extract(file, extract_path)
                    bar.update(1)
        print(f"Arquivos extraídos para: {extract_path}")
        
        # Remove o arquivo ZIP após extração
        os.remove(zip_path)
        print("Arquivo ZIP removido.")
        
    except requests.exceptions.RequestException as e:
        print(f"Erro ao baixar o arquivo: {e}")
    except zipfile.BadZipFile:
        print("Erro: O arquivo baixado não é um ZIP válido.")

def buscar_votos_por_estado(ano, estado):
    estado = estado.strip().upper()  # Remover espaços extras e garantir maiúsculas
    file_name = f"votacao_candidato_munzona_{ano}_{estado}.csv"
    file_path = os.path.join("zip", str(ano), file_name)
    
    if not os.path.exists(file_path):
        print(f"Arquivo não encontrado: {file_path}")
        return
    
    try:
        # Carregar o CSV sem a limitação de 'usecols' para garantir que todas as colunas sejam carregadas
        df = pd.read_csv(file_path, sep=';', encoding='latin1')

        # Remover espaços no final do valor de "NM_URNA_CANDIDATO"
        df['NM_URNA_CANDIDATO'] = df['NM_URNA_CANDIDATO'].str.strip()

        # Verifica se a coluna "QT_VOTOS_NOMINAIS_VALIDOS" existe, senão usa "QT_VOTOS_NOMINAIS"
        if "QT_VOTOS_NOMINAIS_VALIDOS" in df.columns:
            votos_coluna = "QT_VOTOS_NOMINAIS_VALIDOS"
        elif "QT_VOTOS_NOMINAIS" in df.columns:
            votos_coluna = "QT_VOTOS_NOMINAIS"
        else:
            print("Nenhuma coluna de votos válida encontrada.")
            return
        
        # Agrupar e somar votos por candidato, cargo e turno
        df[votos_coluna] = pd.to_numeric(df[votos_coluna], errors='coerce').fillna(0)
        resultado = df.groupby(
            ["NR_TURNO", "DS_CARGO", "NM_URNA_CANDIDATO", "DS_SIT_TOT_TURNO", "NM_UE"]
        )[votos_coluna].sum().reset_index()
        
        # Ordenar por turno e votos
        resultado = resultado.sort_values(by=["NR_TURNO", "DS_CARGO", votos_coluna], ascending=[True, True, False])
        
        # Substituir "QT_VOTOS_NOMINAIS" por "QT_VOTOS_NOMINAIS_VALIDOS" no JSON se necessário
        if votos_coluna == "QT_VOTOS_NOMINAIS":
            resultado = resultado.rename(columns={"QT_VOTOS_NOMINAIS": "QT_VOTOS_NOMINAIS_VALIDOS"})
        
        # Converter para JSON
        resultado_json = resultado.to_json(orient="records", force_ascii=False, indent=4)
        
        # Salvar em um arquivo JSON dentro da pasta do ano
        json_path = os.path.join("resultados", str(ano), f"votacao_nominal/votos_{ano}_{estado}.json")
        os.makedirs(os.path.dirname(json_path), exist_ok=True)
        with open(json_path, "w", encoding="utf-8") as json_file:
            json_file.write(resultado_json)
        
        print(f"Resultado salvo em: {json_path}")
        return resultado_json
    except Exception as e:
        print(f"Erro ao processar o arquivo: {e}")

# Lista com as siglas dos estados brasileiros
estados_brasileiros = [
    "AC", "AL", "AP", "AM", "BA", "CE", "DF", "ES", "GO", "MA", "MT", "MS", "MG", 
    "PA", "PB", "PR", "PE", "PI", "RJ", "RN", "RS", "RO", "RR", "SC", "SP", "SE", 
    "TO", "BR"
]

while True:
    ano = input("Digite o ano desejado (ou 'sair' para encerrar): ").strip()
    if ano.lower() == "sair":
        break
    baixar_e_descompactar(ano)
    
    # Loop para gerar o JSON para todos os estados
    for estado in estados_brasileiros:
        print(f"Gerando resultados para o estado: {estado} ({ano})")
        buscar_votos_por_estado(ano, estado)
