@php
    $left = $branch->subscriptionDaysLeft();
    if ($branch->subscription_exempt) { [$cls, $label] = ['badge-blue', 'Obunasiz (doim faol)']; }
    elseif ($branch->subscriptionExpired()) { [$cls, $label] = ['badge-red', 'Tugagan']; }
    elseif ($left !== null && $left <= 7) { [$cls, $label] = ['badge-amber', 'Tugashiga oz qoldi']; }
    else { [$cls, $label] = ['badge-green', 'Faol']; }
@endphp
<span class="{{ $cls }}">{{ $label }}</span>
