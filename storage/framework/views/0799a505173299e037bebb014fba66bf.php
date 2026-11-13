<!DOCTYPE html>
<html lang="uz">
<head>
<meta charset="utf-8">
<?php $gold = '#b8923a'; $ink = '#1f2937'; ?>
<style>
    @page { margin: 0; }
    body { font-family: 'DejaVu Sans', sans-serif; margin: 0; padding: 0; color: <?php echo e($ink); ?>; }
    .abs { position: absolute; }
    .center { text-align: center; }
    .outer { left: 16pt; top: 16pt; width: 804pt; height: 557pt; border: 3pt solid <?php echo e($accent); ?>; }
    .inner { left: 25pt; top: 25pt; width: 790pt; height: 541pt; border: 0.75pt solid <?php echo e($gold); ?>; }
    .band { left: 16pt; top: 16pt; width: 810pt; height: 8pt; background: <?php echo e($accent); ?>; }
    .corner { width: 34pt; height: 34pt; }
    .tl { left: 34pt; top: 34pt; border-top: 3pt solid <?php echo e($gold); ?>; border-left: 3pt solid <?php echo e($gold); ?>; }
    .tr { right: 34pt; top: 34pt; border-top: 3pt solid <?php echo e($gold); ?>; border-right: 3pt solid <?php echo e($gold); ?>; }
    .bl { left: 34pt; bottom: 34pt; border-bottom: 3pt solid <?php echo e($gold); ?>; border-left: 3pt solid <?php echo e($gold); ?>; }
    .br { right: 34pt; bottom: 34pt; border-bottom: 3pt solid <?php echo e($gold); ?>; border-right: 3pt solid <?php echo e($gold); ?>; }
    .head { left: 60pt; right: 60pt; top: 42pt; }
    .logo { height: 46pt; }
    .branch { font-size: 9pt; letter-spacing: 3pt; color: #6b7280; margin-top: 4pt; }
    .title { left: 60pt; right: 60pt; top: 112pt; }
    h1 { margin: 0; font-size: 38pt; letter-spacing: 12pt; color: <?php echo e($accent); ?>; font-weight: bold; }
    .sub { font-size: 9pt; letter-spacing: 4pt; color: <?php echo e($gold); ?>; margin-top: 2pt; }
    .rule { width: 120pt; border-top: 1.5pt solid <?php echo e($gold); ?>; margin: 10pt auto 0; }
    .who { left: 60pt; right: 60pt; top: 186pt; }
    .label { font-size: 9pt; letter-spacing: 3pt; color: #6b7280; }
    .name { font-size: 32pt; font-weight: bold; margin: 8pt 50pt 0; padding-bottom: 8pt; border-bottom: 0.75pt solid #d1d5db; }
    .course { font-size: 17pt; font-weight: bold; color: <?php echo e($accent); ?>; margin-top: 14pt; }
    .body { font-size: 11pt; color: #4b5563; line-height: 1.5; margin: 8pt 90pt 0; }
    .foot { left: 62pt; right: 62pt; top: 402pt; height: 128pt; }
    .sig { height: 42pt; }
    .line { border-top: 0.75pt solid #9ca3af; padding-top: 4pt; width: 180pt; margin: 0 auto; font-size: 10pt; }
    .small { font-size: 8pt; color: #6b7280; }
    .qr { width: 74pt; height: 74pt; }
</style>
</head>
<body>
<div class="abs band"></div>
<div class="abs outer"></div>
<div class="abs inner"></div>
<div class="abs corner tl"></div><div class="abs corner tr"></div><div class="abs corner bl"></div><div class="abs corner br"></div>

<div class="abs head center">
    <?php if($logo): ?><img class="logo" src="<?php echo e($logo); ?>" alt=""><?php endif; ?>
    <div class="branch"><?php echo e(mb_strtoupper((string) $branch?->name)); ?></div>
</div>

<div class="abs title center">
    <h1><?php echo e($title); ?></h1>
    <div class="sub">MUVAFFAQIYAT SERTIFIKATI</div>
    <div class="rule"></div>
</div>

<div class="abs who center">
    <div class="label">SERTIFIKAT EGASI</div>
    <div class="name"><?php echo e($c->student_name); ?></div>
    <?php if($c->group_id): ?><div class="course"><?php echo e($c->course_name); ?><?php if($c->level_name): ?> · <?php echo e($c->level_name); ?><?php endif; ?></div><?php endif; ?>
    <div class="body"><?php echo e(mb_strtoupper(mb_substr($body, 0, 1)).mb_substr($body, 1)); ?></div>
</div>

<div class="abs foot">
    <table width="100%" cellspacing="0" cellpadding="0">
        <tr>
            <td width="34%" align="center" valign="bottom">
                <?php if($signature): ?><img class="sig" src="<?php echo e($signature); ?>" alt=""><br><?php endif; ?>
                <div class="line"><b><?php echo e($signer); ?></b><br><span class="small"><?php echo e($signerTitle); ?></span></div>
            </td>
            <td width="32%" align="center" valign="bottom">
                <div class="small" style="letter-spacing:2pt">BERILGAN SANA</div>
                <div style="font-size:12pt;font-weight:bold;margin-bottom:8pt"><?php echo e($c->issued_on->format('d.m.Y')); ?></div>
                <div class="small" style="letter-spacing:2pt">SERTIFIKAT RAQAMI</div>
                <div style="font-size:10pt;font-weight:bold"><?php echo e($c->number); ?></div>
            </td>
            <td width="34%" align="center" valign="bottom">
                <img class="qr" src="<?php echo e($qr); ?>" alt=""><br>
                <span class="small">Sertifikat haqiqiyligini tekshirish<br>uchun QR kodni skanerlang</span>
            </td>
        </tr>
    </table>
</div>

</body>
</html>
<?php /**PATH C:\xampp\htdocs\Edunova_ATKO\resources\views/certificates/pdf.blade.php ENDPATH**/ ?>