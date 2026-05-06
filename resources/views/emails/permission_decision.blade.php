<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Décision sur votre demande</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 32px 16px; }
  .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
  .header-approved { background: linear-gradient(135deg, #064e3b 0%, #065f46 100%); padding: 32px; text-align: center; }
  .header-rejected { background: linear-gradient(135deg, #7f1d1d 0%, #991b1b 100%); padding: 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 22px; margin: 0 0 4px; }
  .header p { color: rgba(255,255,255,.7); margin: 0; font-size: 14px; }
  .body { padding: 32px; }
  .status-pill-approved { display:inline-block;background:#d1fae5;color:#065f46;padding:6px 18px;border-radius:99px;font-weight:700;font-size:14px;margin-bottom:20px; }
  .status-pill-rejected { display:inline-block;background:#fee2e2;color:#991b1b;padding:6px 18px;border-radius:99px;font-weight:700;font-size:14px;margin-bottom:20px; }
  .field-row { display:flex;justify-content:space-between;padding:10px 0;border-bottom:1px solid #f1f5f9;font-size:14px; }
  .field-label { color:#64748b; }
  .field-value { color:#0f172a;font-weight:600; }
  .comment-box { background:#f8fafc;border-left:3px solid #64748b;border-radius:0 8px 8px 0;padding:14px;margin-top:20px;font-size:13px;color:#475569; }
  .cta { text-align:center;margin-top:28px; }
  .cta a { display:inline-block;background:#1e293b;color:#fff;text-decoration:none;padding:12px 28px;border-radius:10px;font-weight:600;font-size:14px; }
  .footer { background:#f8fafc;padding:20px 32px;text-align:center;font-size:12px;color:#94a3b8; }
</style>
</head>
<body>
<div class="card">
  <div class="header {{ $decision === 'approved' ? 'header-approved' : 'header-rejected' }}">
    <h1>{{ $decision === 'approved' ? '✅ Demande approuvée' : '❌ Demande refusée' }}</h1>
    <p>Une décision a été prise sur votre demande de permission</p>
  </div>
  <div class="body">
    <p style="color:#334155;font-size:15px;">
      Bonjour <strong>{{ $request->user->name }}</strong>,
    </p>
    <p style="color:#475569;font-size:14px;margin-bottom:20px;">
      Votre demande de <strong>{{ $request->type->name }}</strong> a été
      <strong>{{ $decision === 'approved' ? 'approuvée' : 'refusée' }}</strong>
      par <strong>{{ $request->decider?->name ?? 'un administrateur' }}</strong>.
    </p>

    <span class="{{ $decision === 'approved' ? 'status-pill-approved' : 'status-pill-rejected' }}">
      {{ $decision === 'approved' ? '✓ Approuvé' : '✕ Refusé' }}
    </span>

    @foreach($request->type->fields_config as $field)
      @php $value = $request->fields_data[$field['key']] ?? null; @endphp
      @if($value && $field['type'] !== 'textarea')
      <div class="field-row">
        <span class="field-label">{{ $field['label'] }}</span>
        <span class="field-value">
          @if($field['type'] === 'date') {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
          @else {{ $value }}
          @endif
        </span>
      </div>
      @endif
    @endforeach

    @if($request->decision_comment)
    <div class="comment-box">
      <strong style="font-size:12px;text-transform:uppercase;letter-spacing:.05em;">Commentaire</strong>
      <p style="margin:8px 0 0;">{{ $request->decision_comment }}</p>
    </div>
    @endif

    <div class="cta">
      <a href="{{ route('permissions.index') }}">Voir mes demandes →</a>
    </div>
  </div>
  <div class="footer">
    Plateforme de gestion des stagiaires TFG — Notification automatique
  </div>
</div>
</body>
</html>
