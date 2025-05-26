<template>
  <div class="flex justify-between gap-4">
    <div
      class="rounded-full items-center flex w-1/2 h-8 place-content-center gap-4 justify-center bg-[#cbeaf8] text-[#253b80]"
      style="font-size: 0.6rem"
    >
      <img
        src="//moremoneymorelove.de/cdn/shop/files/paypal_icon_65b1e7e0-16be-46f6-9975-522e6b7b064b_medium.png?v=1722667640"
        title=""
        alt=""
        height=""
        width=""
        class="object-contain w-8"
      />
      <span>{{ $t("common.product.secureAndFast") }}</span>
    </div>

    <div
      class="rounded-full items-center flex w-1/2 h-8 place-content-center gap-4 justify-center bg-[#cdd5ff] text-[#142688]"
      style="font-size: 0.4rem"
    >
      <img
        src="//moremoneymorelove.de/cdn/shop/files/visa_icon_d7450944-e663-4075-b69e-f97704344c33_medium.png?v=1722668083"
        title=""
        alt=""
        height=""
        width=""
        class="object-contain w-8"
        loading="lazy" 
      />
      <span>{{ $t("common.product.creditAndMobile") }}</span>
    </div>
  </div>
</template>