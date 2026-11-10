<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>در حال انتقال به درگاه به‌پرداخت ملت...</title>
</head>
<body onload="document.forms['mellat_form'].submit();" style="text-align: center; padding-top: 50px; font-family: Tahoma, sans-serif;">
<p>در حال انتقال امن به درگاه به‌پرداخت ملت، لطفاً شکیبا باشید...</p>
<form name="mellat_form" method="POST" action="{{ $startPayUrl }}">
    <input type="hidden" name="RefId" value="{{ $refId }}">
</form>
</body>
</html>
