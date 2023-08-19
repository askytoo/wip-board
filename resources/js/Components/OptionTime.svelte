<script>
    export let selectedOption;
    export let step = 900;

    const generateOptions = () => {
        const stepMinutes = step / 60;
        const options = [];
        for (let i = 0; i < 24; i++) {
            for (let j = 0; j < 60 / stepMinutes; j++) {
                options.push(
                    `${i.toString().padStart(2, "0")}:${(j * stepMinutes)
                        .toString()
                        .padStart(2, "0")}`
                );
            }
        }
        return options;
    };
</script>

{#each generateOptions() as option}
    <option value={option} selected={option === selectedOption}>
        {option}
    </option>
{/each}
