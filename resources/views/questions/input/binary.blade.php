<div class="flex items-center gap-6">
    <label class="inline-flex items-center gap-2">
        <input type="radio" name="answers[{{ $question->id }}]" value="yes"> Yes / True
    </label>
    <label class="inline-flex items-center gap-2">
        <input type="radio" name="answers[{{ $question->id }}]" value="no"> No / False
    </label>
</div>
