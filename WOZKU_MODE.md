# Wozku Mode: RAG-Powered Quiz Generation

AI-powered quiz generation using Retrieval-Augmented Generation (RAG) to create company-specific questions from Wozku's documentation.

## Architecture

**Python FastAPI Service** (`rag-service/`)
- LangChain for RAG pipeline
- ChromaDB for vector storage
- OpenAI embeddings (text-embedding-3-small)
- GPT-4o-mini for generation

**Laravel Integration**
- HTTP client calls Python API
- Stores questions with source attribution
- Displays sources on result page

## Setup

### 1. Install Python Service

```bash
cd rag-service
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
```

Add your OpenAI key to `rag-service/.env`:
```
OPENAI_API_KEY=sk-...
```

### 2. Start Python Service

```bash
cd rag-service
source venv/bin/activate
uvicorn main:app --port 8001
```

Service runs on `http://localhost:8001`

### 3. Ingest Wozku Content

```bash
curl -X POST http://localhost:8001/ingest \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://wozku.com/", "https://wozku.com/about-us", "https://dev.wozku.com/faq"]}'
```

This scrapes URLs, chunks text, generates embeddings, and stores in ChromaDB.

### 4. Configure Laravel

Add to `.env`:
```
RAG_SERVICE_URL=http://localhost:8001
```

## Usage

1. Navigate to any quiz edit page
2. Click **✨ Generate with AI**
3. Enter:
   - Topic (e.g., "Wozku's advocacy-led growth")
   - Question count (1-20)
   - Types (single choice, multiple choice, text)
   - Difficulty (easy, medium, hard)
4. Submit → Questions generated and added to quiz

## API Endpoints

**POST /ingest**
```json
{
  "urls": ["https://wozku.com/"]
}
```

**POST /generate**
```json
{
  "topic": "Wozku's approach",
  "count": 5,
  "types": ["single_choice", "text"],
  "difficulty": "medium"
}
```

**GET /health**
Check service status

## How It Works

1. **Ingestion**: URLs → text extraction → chunking → embeddings → ChromaDB
2. **Retrieval**: Query → embedding → similarity search → top-K chunks
3. **Generation**: Chunks + prompt → GPT-4o-mini → structured questions
4. **Storage**: Laravel saves questions with source attribution

## Source Attribution

Result page shows:
- ✨ Badge for AI-generated questions
- Expandable "View sources" with relevant text excerpts
- Transparent grounding in actual documentation

## Extending

**Add more URLs:**
```bash
curl -X POST http://localhost:8001/ingest \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://wozku.com/case-studies"]}'
```

**Adjust chunk size:**
Edit `main.py`:
```python
splitter = RecursiveCharacterTextSplitter(
    chunk_size=1000,  # increase for more context
    chunk_overlap=100
)
```

**Change models:**
```python
embeddings = OpenAIEmbeddings(model="text-embedding-3-large")
llm = ChatOpenAI(model="gpt-4", temperature=0.7)
```

## Production Deployment

For production:
1. Use proper process manager (systemd, supervisor)
2. Add authentication to FastAPI
3. Use PostgreSQL + pgvector instead of ChromaDB
4. Add rate limiting
5. Monitor costs (embeddings + LLM calls)

## Cost Estimate

Per generation (5 questions):
- Embeddings: ~$0.0001
- LLM: ~$0.01
- Total: ~$0.01 per quiz

Ingestion (one-time): ~$0.001 per URL
