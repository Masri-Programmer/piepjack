<template>
    <div class="flex flex-col justify-between gap-4 text-xs">
        <div
            class="px-3 rounded-full items-center flex justify-center gap-4 bg-[#cbeaf8] text-[#253b80] h-8 place-content-center w-full"
        >
            <img
                src="//moremoneymorelove.de/cdn/shop/files/paypal_icon_65b1e7e0-16be-46f6-9975-522e6b7b064b_medium.png?v=1722667640"
                title=""
                alt=""
                height=""
                width=""
                class="object-contain w-8 flex-shrink-0"
            />
            <span class="flex-1">{{ $t("common.product.secureAndFast") }}</span>
        </div>

        <div
            class="px-3 rounded-full items-center flex justify-center gap-4 bg-[#cdd5ff] text-[#142688] h-8 place-content-center w-full"
        >
            <img
                src="//moremoneymorelove.de/cdn/shop/files/visa_icon_d7450944-e663-4075-b69e-f97704344c33_medium.png?v=1722668083"
                title=""
                alt=""
                height=""
                width=""
                class="object-contain w-8 flex-shrink-0"
                loading="lazy"
            />
            <span class="flex-1">{{
                $t("common.product.creditAndMobile")
            }}</span>
        </div>
    </div>
</template>
