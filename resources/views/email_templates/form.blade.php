<div class="mb-3">
    <label class="form-label">Template Name</label>
    <input name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Subject</label>
    <input name="subject" class="form-control" value="{{ old('subject', $template->subject) }}" required>
</div>
<div class="mb-3">
    <label class="form-label">Body</label>
    <textarea name="body" rows="12" class="form-control font-monospace" required>{{ old('body', $template->body) }}</textarea>
</div>
<div class="mb-3">
    <label class="form-label">Available variables</label>
    <div class="small text-muted"><code>{{ '{{first_name}}' }}</code> <code>{{ '{{company_name}}' }}</code> <code>{{ '{{industry}}' }}</code> <code>{{ '{{website}}' }}</code> <code>{{ '{{city}}' }}</code> <code>{{ '{{research_summary}}' }}</code> <code>{{ '{{lead_title}}' }}</code></div>
</div>
<div class="form-check mb-4">
    <input type="hidden" name="is_active" value="0">
    <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $template->exists ? $template->is_active : true) ? 'checked' : '' }}>
    <label class="form-check-label" for="is_active">Active</label>
</div>
