<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>{{ config('app.name') }} - Confirmación de pedido</title>
</head>

<body style="font-family: Arial, Helvetica, sans-serif; background-color: #f7f7f7; margin: 0; padding: 0;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" style="padding: 24px;">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation"
                    style="background-color: #ffffff; border-radius: 12px; overflow: hidden;">
                    <tr>
                        <td style="background-color: #0d6efd; color: #ffffff; padding: 24px;">
                            <h1 style="margin: 0; font-size: 24px;">¡Gracias por tu compra!</h1>
                            <p style="margin: 8px 0 0; font-size: 16px;">Hemos recibido tu pedido y estamos procesándolo.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 24px; color: #1f2937;">
                            <p style="margin: 0 0 16px; font-size: 16px;">Hola {{ $confirmation['name'] }},</p>
                            <p style="margin: 0 0 16px; font-size: 15px; line-height: 1.5;">
                                Este es el resumen de tu compra con número de referencia
                                <strong>{{ $confirmation['reference'] }}</strong>.
                            </p>
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                style="margin-bottom: 24px;">
                                <tr>
                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Correo electrónico</td>
                                    <td style="padding: 8px 0; font-size: 14px; text-align: right; color: #111827;">
                                        {{ $confirmation['email'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 8px 0; font-size: 14px; color: #6b7280;">Método de pago</td>
                                    <td style="padding: 8px 0; font-size: 14px; text-align: right; color: #111827;">
                                        {{ $confirmation['payment_method_label'] }}
                                    </td>
                                </tr>
                                @if (!empty($confirmation['notes']))
                                    <tr>
                                        <td style="padding: 8px 0; font-size: 14px; color: #6b7280; vertical-align: top;">Notas</td>
                                        <td style="padding: 8px 0; font-size: 14px; text-align: right; color: #111827;">
                                            {{ $confirmation['notes'] }}
                                        </td>
                                    </tr>
                                @endif
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation"
                                style="border-collapse: collapse; margin-bottom: 24px;">
                                <thead>
                                    <tr>
                                        <th align="left" style="font-size: 13px; text-transform: uppercase; color: #6b7280; padding-bottom: 8px;">
                                            Producto
                                        </th>
                                        <th align="center" style="font-size: 13px; text-transform: uppercase; color: #6b7280; padding-bottom: 8px;">
                                            Cantidad
                                        </th>
                                        <th align="right" style="font-size: 13px; text-transform: uppercase; color: #6b7280; padding-bottom: 8px;">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($confirmation['items'] as $item)
                                        <tr>
                                            <td style="padding: 12px 0; border-top: 1px solid #e5e7eb; font-size: 14px;">
                                                {{ $item['name'] }}
                                            </td>
                                            <td align="center" style="padding: 12px 0; border-top: 1px solid #e5e7eb; font-size: 14px;">
                                                {{ $item['quantity'] }}
                                            </td>
                                            <td align="right" style="padding: 12px 0; border-top: 1px solid #e5e7eb; font-size: 14px;">
                                                ${{ number_format($item['price'] * $item['quantity'], 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <table width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin-bottom: 24px;">
                                <tr>
                                    <td style="font-size: 16px; font-weight: bold; color: #111827;">Total pagado</td>
                                    <td align="right" style="font-size: 18px; font-weight: bold; color: #0d6efd;">
                                        ${{ number_format($confirmation['total'], 2) }}
                                    </td>
                                </tr>
                            </table>
                            <p style="margin: 0; font-size: 14px; color: #6b7280;">
                                Si tienes alguna duda sobre tu pedido, responde a este correo y estaremos encantados de ayudarte.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color: #f3f4f6; padding: 16px; text-align: center; font-size: 12px; color: #6b7280;">
                            {{ config('app.name') }} &bull; {{ config('app.url') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
