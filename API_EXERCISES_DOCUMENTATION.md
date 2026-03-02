# API de Exercícios - Documentação

## Descrição

API RESTful para gerenciar exercícios de ditado. Quando um exercício é criado, o sistema:
1. Divide o conteúdo em palavras
2. Processa as sílabas de cada palavra
3. Gera áudio TTS para a frase completa
4. Gera áudio TTS para cada palavra individual

## Endpoints

### 1. Criar Exercício
**POST** `/api/exercises`

Cria um novo exercício e processa automaticamente (divide em palavras, cria sílabas, gera áudios).

#### Parâmetros de entrada:
```json
{
  "number": 1,
  "difficulty": "easy",
  "content": "A rápida raposa salta sobre o cão preguiçoso"
}
```

| Campo | Tipo | Obrigatório | Descrição |
|-------|------|-------------|-----------|
| `number` | integer | Sim | Número/ID do exercício (≥ 1) |
| `difficulty` | string | Sim | Nível de dificuldade: `easy`, `medium` ou `hard` |
| `content` | string | Sim | Conteúdo/sentença do exercício |

#### Exemplo cURL:
```bash
curl -X POST http://localhost:8000/api/exercises \
  -H "Content-Type: application/json" \
  -d '{
    "number": 1,
    "difficulty": "easy",
    "content": "A rápida raposa salta sobre o cão preguiçoso"
  }'
```

#### Resposta (201 Created):
```json
{
  "success": true,
  "message": "Exercício criado e processado com sucesso",
  "exercise": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "number": 1,
    "difficulty": "easy",
    "content": "A rápida raposa salta sobre o cão preguiçoso",
    "words_json": "[{\"word\":\"a\",\"order\":1,\"syllables\":\"a\",\"word_id\":\"...\"}, ...]",
    "audio_url_1": "storage/audio/sentences/exercise-550e8400-e29b-41d4-a716-446655440000.mp3",
    "audio_url_2": null
  }
}
```

---

### 2. Listar Exercícios
**GET** `/api/exercises`

Lista todos os exercícios com paginação.

#### Parâmetros (query):
- `page` (opcional): Número da página (default: 1)
- `per_page` (opcional): Itens por página (default: 15)

#### Exemplo cURL:
```bash
curl -X GET "http://localhost:8000/api/exercises?page=1"
```

#### Resposta (200 OK):
```json
{
  "success": true,
  "data": [
    {
      "id": "550e8400-e29b-41d4-a716-446655440000",
      "number": 1,
      "difficulty": "easy",
      "content": "A rápida raposa...",
      "sentence": "A rápida raposa...",
      "words_json": "[...]",
      "audio_url_1": "storage/audio/sentences/...",
      "audio_url_2": null,
      "created_at": "2026-03-02T10:30:00Z",
      "updated_at": "2026-03-02T10:30:00Z"
    }
  ],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 75
  }
}
```

---

### 3. Obter Exercício Específico
**GET** `/api/exercises/{id}`

Retorna um exercício com suas palavras relacionadas.

#### Exemplo cURL:
```bash
curl -X GET "http://localhost:8000/api/exercises/550e8400-e29b-41d4-a716-446655440000"
```

#### Resposta (200 OK):
```json
{
  "success": true,
  "exercise": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "number": 1,
    "difficulty": "easy",
    "content": "A rápida raposa...",
    "sentence": "A rápida raposa...",
    "words_json": "[...]",
    "audio_url_1": "storage/audio/sentences/...",
    "audio_url_2": null,
    "words": [
      {
        "id": "word-id-1",
        "word": "rápida",
        "syllables": "rá-pi-da",
        "difficulty": 1,
        "audio_url": "storage/audio/words/rapida.mp3"
      }
    ]
  }
}
```

---

### 4. Atualizar Exercício
**PUT** `/api/exercises/{id}`

Atualiza um exercício e reprocessa se o conteúdo/dificuldade mudar.

#### Parâmetros (opcionais):
```json
{
  "number": 2,
  "difficulty": "medium",
  "content": "Novo conteúdo do exercício"
}
```

#### Exemplo cURL:
```bash
curl -X PUT "http://localhost:8000/api/exercises/550e8400-e29b-41d4-a716-446655440000" \
  -H "Content-Type: application/json" \
  -d '{
    "difficulty": "medium",
    "content": "Novo conteúdo do exercício"
  }'
```

#### Resposta (200 OK):
```json
{
  "success": true,
  "message": "Exercício atualizado com sucesso",
  "exercise": {
    "id": "550e8400-e29b-41d4-a716-446655440000",
    "number": 2,
    "difficulty": "medium",
    "content": "Novo conteúdo do exercício",
    "words_json": "[...]",
    "audio_url_1": "storage/audio/sentences/..."
  }
}
```

---

### 5. Deletar Exercício
**DELETE** `/api/exercises/{id}`

Remove um exercício e seus áudios associados.

#### Exemplo cURL:
```bash
curl -X DELETE "http://localhost:8000/api/exercises/550e8400-e29b-41d4-a716-446655440000"
```

#### Resposta (200 OK):
```json
{
  "success": true,
  "message": "Exercício eliminado com sucesso"
}
```

---

## Procesamento Automático

Quando um exercício é criado ou atualizado, o sistema **automaticamente**:

1. **Divide o conteúdo em palavras** - Remove pontuação, mantém acentos
2. **Processa sílabas** - Divide cada palavra em sílabas usando o `PortugueseSyllableSplitter`
3. **Gera/reutiliza palavras** - Verifica se a palavra já existe na BD, caso contrário cria
4. **Cria relações** - Estabelece a relação entre exercício e palavras (`ExerciseWord`)
5. **Gera áudio da frase** - Usa Google Translate TTS ou SoundOfText API para a frase completa
6. **Gera áudio das palavras** - Cria áudio TTS para cada palavra individual

## Códigos de Resposta

| Código | Significado |
|--------|-------------|
| 201 | Exercício criado com sucesso |
| 200 | Sucesso (listagem, obtenção, atualização, deleção) |
| 400 | Dados inválidos / Dificuldade inválida |
| 404 | Exercício não encontrado |
| 422 | Validação falhou |
| 500 | Erro interno do servidor |

## Exemplo de Integração (JavaScript)

```javascript
// Criar exercício
const response = await fetch('/api/exercises', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    number: 1,
    difficulty: 'easy',
    content: 'A rápida raposa salta sobre o cão preguiçoso'
  })
});

const data = await response.json();
console.log('Exercício criado:', data.exercise);

// Obter exercício
const getResponse = await fetch(`/api/exercises/${data.exercise.id}`);
const exercise = await getResponse.json();
console.log('Palavras do exercício:', exercise.exercise.words);
```

## Notas

- Os IDs dos exercícios são UUIDs (strings)
- Os áudios são armazenados em `storage/public/audio/`
- O campo `words_json` contém um array serializado com informações das palavras
- A dificuldade é usada para processar as palavras também (`easy=1`, `medium=2`, `hard=3`)
- Os áudios são gerados de forma assíncrona (não bloqueiam a resposta)
