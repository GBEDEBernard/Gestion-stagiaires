<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Demande de permission</title>
<style>
  body { font-family: 'Segoe UI', sans-serif; background: #f1f5f9; margin: 0; padding: 32px 16px; }
  .card { background: #fff; max-width: 560px; margin: 0 auto; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,.08); }
  .header { background: linear-gradient(135deg, #1e293b 0%, #334155 100%); padding: 32px; text-align: center; }
  .header h1 { color: #fff; font-size: 20px; margin: 0 0 4px; }
  .header p { color: #94a3b8; margin: 0; font-size: 14px; }
  .badge { display: inline-block; padding: 4px 14px; border-radius: 99px; font-size: 12px; font-weight: 600; margin-top: 12px; background: #3b82f6; color: #fff; }
  .body { padding: 32px; }
  .greeting { font-size: 15px; color: #334155; margin-bottom: 20px; }
  .field-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
  .field-label { color: #64748b; font-weight: 500; }
  .field-value { color: #0f172a; font-weight: 600; text-align: right; max-width: 60%; }
  .note-box { background: #f8fafc; border-left: 3px solid #3b82f6; border-radius: 0 8px 8px 0; padding: 14px; margin-top: 20px; font-size: 13px; color: #475569; }
  .cta { text-align: center; margin-top: 28px; }
  .cta a { display: inline-block; background: #1e293b; color: #fff; text-decoration: none; padding: 12px 28px; border-radius: 10px; font-weight: 600; font-size: 14px; }
  .footer { background: #f8fafc; padding: 20px 32px; text-align: center; font-size: 12px; color: #94a3b8; }
</style>
</head>
<body>
<div class="card">
  <div class="header">
    <h1>📋 Nouvelle demande de permission</h1>
    <p>Vous avez été désigné comme destinataire</p>
    <span class="badge">{{ $request->type->name }}</span>
  </div>
  <div class="body">
    <p class="greeting">Bonjour <strong>{{ $signataire->nom }}</strong>,</p>
    <p style="color:#475569;font-size:14px;margin-bottom:24px;">
      <strong>{{ $request->user->name }}</strong> a soumis une demande de
      <strong>{{ $request->type->name }}</strong> qui vous est adressée.
    </p>

    @foreach($request->type->fields_config as $field)
      @php $value = $request->fields_data[$field['key']] ?? null; @endphp
      @if($value && $field['type'] !== 'textarea')
      <div class="field-row">
        <span class="field-label">{{ $field['label'] }}</span>
        <span class="field-value">
          @if($field['type'] === 'date') {{ \Carbon\Carbon::parse($value)->format('d/m/Y') }}
          @elseif($field['type'] === 'time') {{ $value }}
          @else {{ $value }}
          @endif
        </span>
      </div>
      @endif
    @endforeach

    @foreach($request->type->fields_config as $field)
      @if($field['type'] === 'textarea' && isset($request->fields_data[$field['key']]))
      <div class="note-box" style="margin-top:16px;">
        <strong style="color:#334155;font-size:12px;text-transform:uppercase;letter-spacing:.05em;">{{ $field['label'] }}</strong>
        <p style="margin:8px 0 0;color:#475569;">{{ $request->fields_data[$field['key']] }}</p>
      </div>
      @endif
    @endforeach

    @if($request->note)
    <div class="note-box" style="margin-top:12px;border-color:#8b5cf6;">
      <strong style="color:#334155;font-size:12px;">Note complémentaire</strong>
      <p style="margin:8px 0 0;">{{ $request->note }}</p>
    </div>
    @endif

    <div class="cta">
      <a href="{{ route('admin.permissions.index') }}">Voir sur la plateforme →</a>
    </div>
  </div>
  <div class="footer">
    Cet email a été envoyé automatiquement par la plateforme de gestion des stagiaires TFG.<br>
    La validation se fait exclusivement sur la plateforme.
  </div>
</div>
</body>
</html>
