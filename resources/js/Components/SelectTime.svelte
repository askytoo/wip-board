<script>
    import Flatpickr from "svelte-flatpickr";
    import "flatpickr/dist/flatpickr.css";

    export let value, formattedValue;
    export let classes = "";

    const options = {
        enableTime: true,
        onChange(selectedDates, dateStr) {
            console.log("flatpickr hook", selectedDates, dateStr);
        },
    };

    $: console.log({ value, formattedValue });

    function handleChange(event) {
        const [selectedDates, dateStr] = event.detail;
        console.log({ selectedDates, dateStr });
    }

</script>

<Flatpickr
    {options}
    bind:value
    bind:formattedValue
    dateFormat="Y/m/d"
    on:change={handleChange}
    name="date"
    class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full {classes}"
    min="today"
/>
