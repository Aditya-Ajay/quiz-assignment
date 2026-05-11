# Setup Checklist

## Python Service Setup

- [ ] Navigate to `rag-service/`
- [ ] Create virtual environment: `python3 -m venv venv`
- [ ] Activate: `source venv/bin/activate`
- [ ] Install dependencies: `pip install -r requirements.txt`
- [ ] Copy `.env.example` to `.env`
- [ ] Add `OPENAI_API_KEY` to `.env`
- [ ] Start service: `uvicorn main:app --port 8001`
- [ ] Verify: `curl http://localhost:8001/health`

## Content Ingestion

- [ ] Ingest Wozku content:
```bash
curl -X POST http://localhost:8001/ingest \
  -H "Content-Type: application/json" \
  -d '{"urls": ["https://wozku.com/", "https://wozku.com/about-us", "https://dev.wozku.com/faq"]}'
```
- [ ] Wait ~2 minutes for completion
- [ ] Verify: Check response shows documents and chunks created

## Laravel Configuration

- [ ] Add to `.env`: `RAG_SERVICE_URL=http://localhost:8001`
- [ ] Start Laravel: `php artisan serve`
- [ ] Verify route exists: `php artisan route:list --path=generate`

## Test Generation

- [ ] Visit http://127.0.0.1:8000/quizzes
- [ ] Edit any quiz
- [ ] Click "✨ Generate with AI"
- [ ] Enter topic: "Wozku's advocacy-led growth"
- [ ] Count: 3, Types: all, Difficulty: medium
- [ ] Click "Generate Questions"
- [ ] Wait ~10 seconds
- [ ] Verify questions appear in quiz

## Test Full Flow

- [ ] Click "Take Quiz"
- [ ] Answer questions
- [ ] Submit
- [ ] View result page
- [ ] Verify ✨ badge on AI questions
- [ ] Click "View sources"
- [ ] Verify source excerpts display

## Troubleshooting

**Python service won't start**
- Check Python version: `python3 --version` (need 3.8+)
- Check port 8001 available: `lsof -i :8001`
- Check OpenAI key in `.env`

**Ingestion fails**
- Check internet connection
- Verify URLs are accessible
- Check OpenAI API key has credits

**Generation fails**
- Verify Python service running: `curl http://localhost:8001/health`
- Check Laravel `.env` has `RAG_SERVICE_URL`
- Check Laravel logs: `tail -f storage/logs/laravel.log`

**No sources shown**
- Verify ingestion completed successfully
- Check question config has `source_chunks` array
- Inspect browser console for errors

## Demo Preparation

- [ ] Pre-ingest content (don't do live)
- [ ] Have quiz ready to edit
- [ ] Test generation once before demo
- [ ] Prepare talking points about RAG
- [ ] Have backup questions if API fails
