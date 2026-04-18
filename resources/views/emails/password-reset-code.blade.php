<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #eee; border-radius: 10px; }
        .header { text-align: center; margin-bottom: 30px; }
        .code-box { background: #f4f7ff; border: 2px dashed #4f46e5; border-radius: 8px; padding: 20px; text-align: center; font-size: 32px; font-weight: bold; color: #4f46e5; letter-spacing: 5px; margin: 20px 0; }
        .footer { font-size: 12px; color: #777; margin-top: 30px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Recuperação de Senha</h2>
        </div>
        <p>Olá,</p>
        <p>Recebemos uma solicitação para redefinir a senha da sua conta na <strong>MembersArea</strong>.</p>
        <p>Use o código abaixo para prosseguir com a recuperação. Este código é válido por <strong>15 minutos</strong>.</p>
        
        <div class="code-box">
            {{ $code }}
        </div>
        
        <p>Se você não solicitou a redefinição de senha, ignore este e-mail.</p>
        
        <div class="footer">
            <p>&copy; {{ date('Y') }} MembersArea. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
