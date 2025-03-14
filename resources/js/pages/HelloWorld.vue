<script setup>
    import { watch, ref, onMounted } from 'vue';
    import Pikaday from 'pikaday';
    import dayjs from "dayjs";

    const myDatepicker = ref(null);
    const dateValue = ref(null);

    onMounted(function () {
        dateValue.value = new Pikaday({
            field: myDatepicker.value,
            format: 'D/M/YYYY',
            onSelect: dateSelected
        });
    });

    function dateSelected () {
        console.log(dayjs(dateValue.value.getDate()).format('YYYY-MM-DD'));
    }
</script>

<template>
    <main>
        <section>coba ya ges yaaa</section>
        <input type="text" class="input pika-single" ref="myDatepicker" value="Pick a day"/>
        <div>
            <p>res : {{ myDatepicker }}</p>
        </div>
    </main>
</template>

<style scoped>

</style>
