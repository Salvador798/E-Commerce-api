<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Código de recuperación</title>
</head>
<body style="margin:0;padding:0;background:#f6f7fb;font-family:Arial, sans-serif;color:#111;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f6f7fb;padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="background:#ffffff;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="padding:28px 32px;background:#111827;color:#ffffff;">
                            <h1 style="margin:0;font-size:20px;font-weight:600;">Recuperación de contraseña</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 32px;">
                            <p style="margin:0 0 12px 0;font-size:15px;line-height:1.6;">Hola,</p>
                            <p style="margin:0 0 16px 0;font-size:15px;line-height:1.6;">Recibimos una solicitud para restablecer tu contraseña. Usa el siguiente código:</p>
                            <div style="margin:20px 0 24px 0;padding:14px 18px;border:1px solid #e5e7eb;border-radius:10px;background:#f9fafb;display:inline-block;">
                                <span style="font-size:22px;letter-spacing:2px;font-weight:700;">{{ $code }}</span>
                            </div>
                            <p style="margin:0 0 8px 0;font-size:13px;line-height:1.6;color:#374151;">Si no solicitaste este código, puedes ignorar este correo.</p>
                            <p style="margin:0;font-size:13px;line-height:1.6;color:#374151;">Gracias.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:18px 32px;background:#f3f4f6;font-size:12px;color:#6b7280;">
                            Este correo fue enviado automáticamente. No respondas a este mensaje.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
