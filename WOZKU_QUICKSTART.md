# Quick Start: Wozku Mode

## Setup (5 minutes)

### 1. Python Service

```bash
cd rag-service
python3 -m venv venv
source venv/bin/activate
pip install -r requirements.txt
cp .env.example .env
```

Add OpenAI key to `rag-service/.env`:
```
OPENAI_API_KEY=sk-...
```

### 2. Start Service

```bash
uvicorn main:app --port 8001
```

### 3. Ingest Content

```bash
curl -X POST http://localhost:8001/ingest \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://wozku.com/", "https://wozku.com/about-us"]}'
```

Takes ~2 minutes. Watch terminal for progress.

### 4. Configure Laravel

Add to `.env`:
```
RAG_SERVICE_URL=http://localhost:8001
```

## Test

1. Start Laravel: `php artisan serve`
2. Visit: http://127.0.0.1:8000/quizzes
3. Edit any quiz → **✨ Generate with AI**
4. Topic: "Wozku's advocacy-led growth"
5. Count: 3, Types: all, Difficulty: medium
6. Generate → wait ~10 seconds
7. Take quiz → view results → expand sources

## Troubleshooting

**"Connection refused"**
- Python service not running
- Check port 8001: `lsof -i :8001`

**"No content ingested"**
- Run ingest curl command above
- Check: `curl http://localhost:8001/health`

**"Invalid JSON"**
- Rare LLM parsing error
- Try different topic or reduce count

## Demo Tips

- Pre-ingest content before demo
- Use 3 questions for speed
- Show source attribution on results
- Explain RAG: "Questions grounded in real docs"
