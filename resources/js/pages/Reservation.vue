<script setup>
import { watch, ref, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import Pikaday from 'pikaday';
import dayjs from "dayjs";

const props = defineProps({
    products: Array,
    availableDates: Array,
    takenTimes: Array,
    price: Number,
});
const myDatepicker = ref(null);
const dateValue = ref(null);
const selectedProduct = ref('');
const selectedTime = ref([]);
const optionTime = ref([]);

onMounted(function () {
    const startTime = dayjs().startOf('day');
    const endTime = dayjs().endOf('day');
    generateTimetable(startTime, endTime);
    dateValue.value = new Pikaday({
        field: myDatepicker.value,
        format: 'D/M/YYYY',
        onSelect: dateSelected,
        disableDayFn: function (date) {
            return !props.availableDates.some(
                (d) => d === dayjs(date.toDateString()).format('YYYY-MM-DD')
            );
        },
    });
});

watch(selectedProduct, function (val) {
    router.get(`/app`, {productId: val}, {
        preserveState: true,
        only: ['availableDates'],
        onSuccess: () => dateValue.value.clear(),
    });
});

watch(selectedTime, function () {
    calculatePrice();
})

/**
 *
 * @param {dayjs.Dayjs} start
 * @param {dayjs.Dayjs} end
 */
function generateTimetable (start, end) {
    let currHour = parseInt(start.format('HH'));
    const endHour = parseInt(end.format('HH'));
    for (let i = 0; i <= endHour - currHour; i++) {
        optionTime.value.push(start.add(i, 'hours').format('HH:mm'));
    }
}

function dateSelected () {
    const payload = {
        productId: selectedProduct.value,
        date: dayjs(dateValue.value.getDate()).format('YYYY-MM-DD'),
    };
    router.get(`/app`, payload, {
        preserveState: true,
        only: ['takenTimes'],
    });
}

/**
 *
 * @param {string} time
 * @return {string}
 */
function calculateTimetableClass (time) {
    if (!props.takenTimes || !dateValue.value.getDate() || props.takenTimes.includes(time)) return 'btn-disabled';
    if (selectedTime.value.includes(time)) return 'btn-primary';
    return '';
}
function calculatePrice () {
    const payload = {
        productId: selectedProduct.value,
        date: dayjs(dateValue.value.getDate()).format('YYYY-MM-DD'),
        numOfSession: selectedTime.value.length,
    };
    router.get(`/app`, payload, {
        preserveState: true,
        only: ['price'],
    });
}
</script>

<template>
    <main>
        <section id="reservation" class="max-w-7xl mx-auto py-20">
            <section class="px-4 flex flex-col items-center">
                <fieldset class="fieldset mb-4">
                    <legend class="fieldset-legend">Masukan nomor HP kamu<span class="font-bold text-error">*</span></legend>
                    <input type="text" class="input min-w-sm max-w-sm" placeholder="Nomor HP"/>
                </fieldset>
                <fieldset class="fieldset mb-4">
                    <legend class="fieldset-legend">Produk yang akan kamu gunakan?<span class="font-bold text-error">*</span></legend>
                    <select class="select min-w-sm max-w-sm" v-model="selectedProduct">
                        <option value="" disabled>Pilih Produk</option>
                        <option v-for="(item, k) in products" :key="k" :value="item.value">{{ item.label }}</option>
                    </select>
                </fieldset>
                <fieldset class="fieldset mb-4">
                    <legend class="fieldset-legend">Pesan untuk hari?<span class="font-bold text-error">*</span></legend>
                    <input :disabled="!props.availableDates" type="text" class="input min-w-sm max-w-sm" ref="myDatepicker" placeholder="Pilih hari"/>
                </fieldset>
                <fieldset class="fieldset mb-4 min-w-sm max-w-sm">
                    <legend class="fieldset-legend">Silahkan pilih sesi<span class="font-bold text-error">*</span></legend>
                    <div class="flex justify-center">
                        <div class="grid grid-cols-4 gap-4 ">
                            <label v-for="(item, k) in optionTime" :key="k">
                                <input
                                    type="checkbox"
                                    :disabled="takenTimes?.includes(item)"
                                    v-model="selectedTime"
                                    class="hidden"
                                    :value="item"
                                    :checked="selectedTime.includes(item)">
                                <span :class="'btn ' + calculateTimetableClass(item)">{{ item }}</span>
                            </label>
                        </div>
                    </div>
                    <span class="fieldset-label">1 jam per sesi</span>
                </fieldset>
            </section>
        </section>
        <section class="dock">
            <button :class="price > 0 ? 'bg-primary' : 'border'" :disabled="!(price > 0)">
                <span class="dock-label">Checkout</span>
                <p>Rp {{ Intl.NumberFormat('id-ID').format(price || 0) }}</p>
            </button>
        </section>
    </main>
</template>

<style scoped>

</style>
