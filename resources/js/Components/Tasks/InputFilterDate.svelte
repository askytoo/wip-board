<script lang="ts">
    import type {
        ColumnDef,
        FiltersColumn,
        Table,
    } from "@tanstack/svelte-table";
    import SveltyPicker from "svelty-picker";
    import { jp } from "svelty-picker/i18n";

    export let column: ColumnDef<any, unknown>;
    export let table: Table<any>;

    type FacetVals = {
        min: Date | undefined;
        max: Date | undefined;
    };

    function getMinMax(columnId?: string): FacetVals {
        if (!columnId) return { min: undefined, max: undefined };
        const column = table.getColumn(columnId);
        if (!column) return { min: undefined, max: undefined };

        const facets = column.getFacetedMinMaxValues();
        if (!facets) return { min: undefined, max: undefined };

        return {
            min: new Date(facets[0]),
            max: column.id === "deadline" ? new Date(facets[1]) : new Date()
        };
    }

    let facetVals = getMinMax(column.id);

    function handleMinValueChange(e: CustomEvent) {
        const date = e.detail as string;
        const value = new Date(date + "T00:00:00");

        (column as unknown as FiltersColumn<any>).setFilterValue(
            (old: [Date, Date]) => [value, old?.[1] ?? facetVals.max]
        );
    }

    function handleMaxValueChange(e: CustomEvent) {
        const date = e.detail as string;
        const value = new Date(date + "T23:59:59");

        (column as unknown as FiltersColumn<any>).setFilterValue(
            (old: [Date, Date]) => [old?.[0] ?? facetVals.min, value]
        );
    }
</script>

<div>
    <div class="ml-3">
        <SveltyPicker
            name="min"
            mode="date"
            manualInput={true}
            format="yyyy-mm-dd"
            displayFormat="yyyy/mm/dd"
            initialDate={facetVals.min}
            inputClasses="w-32 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 mr-4"
            clearBtn={false}
            todayBtn={false}
            required
            i18n={jp}
            startDate={facetVals.min}
            endDate={facetVals.max}
            on:change={handleMinValueChange}
            placeholder="開始日"
        />
    </div>

    <div class="ml-3">
        <SveltyPicker
            name="max"
            mode="date"
            manualInput={true}
            format="yyyy-mm-dd"
            displayFormat="yyyy/mm/dd"
            initialDate={facetVals.max}
            inputClasses="w-32 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 mr-4"
            clearBtn={false}
            todayBtn={false}
            required
            i18n={jp}
            startDate={facetVals.min}
            endDate={facetVals.max}
            on:change={handleMaxValueChange}
            placeholder="終了日"
        />
    </div>
</div>
