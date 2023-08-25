<script lang="ts">
    import type {
        ColumnDef,
        FiltersColumn,
        Table,
    } from "@tanstack/svelte-table";
    import Checkbox from "../Checkbox.svelte";

    import type { Task } from "../../types/task";

    export let column: ColumnDef<Task, unknown>;
    export let table: Table<any>;

    type Facet = {
        name: string;
        value: number;
    };
    type FacetVals = {
        top5: Facet[];
        next20: Facet[];
        hasMore: boolean;
    };

    function getTopFiveFacets(columnId?: string): FacetVals {
        if (!columnId) return { top5: [], next20: [], hasMore: false };
        const column = table.getColumn(columnId);
        if (!column) return { top5: [], next20: [], hasMore: false };

        const facets = column.getFacetedUniqueValues();
        const facetsArr = Array.from(facets, ([name, value]) => ({
            name,
            value,
        }));

        const sortedFacets = facetsArr.sort((a, b) => {
            return b.value - a.value;
        });

        const top5 = sortedFacets.slice(0, 5);
        const next20 = sortedFacets.slice(5, 25);
        const hasMore = sortedFacets.length > 25;

        return { top5, next20, hasMore };
    }

    let facetVals = getTopFiveFacets(column.id);

    let checkedCols = new Set();

    checkedCols.add("未着手");
    (column as unknown as FiltersColumn<any>).setFilterValue(
        Array.from(checkedCols)
    );
    let checkedColsArr = Array.from(checkedCols);

    function handleCheck(e: Event) {
        const target = e.target as HTMLInputElement;
        const checked = target.checked;
        const name = target.name;

        if (checked) {
            checkedCols.add(name);
        } else {
            checkedCols.delete(name);
        }

        (column as unknown as FiltersColumn<any>).setFilterValue(
            Array.from(checkedCols)
        );

        checkedColsArr = Array.from(checkedCols);
    }
</script>

<div class="flex gap-3">
    {#each facetVals.top5 as top5}
        <label class="checkbox">
            <Checkbox
                onChange={handleCheck}
                name={top5.name}
                checked={checkedColsArr.includes(top5.name)}
            />
            <span
                class={checkedColsArr.includes(top5.name)
                    ? "text-indigo-600 dark:text-indigo-400"
                    : ""}
            >
                {top5.name} ({top5.value})
            </span>
        </label>
    {/each}
    {#if facetVals.next20.length > 0}
        <div>
            <details>
                <summary>More</summary>
                <div>
                    {#each facetVals.next20 as next20}
                        <div>
                            <label class="checkbox">
                                <Checkbox
                                    onChange={handleCheck}
                                    name={next20.name}
                                />
                                <span
                                    class={checkedColsArr.includes(next20.name)
                                        ? "text-indigo-600 dark:text-indigo-400"
                                        : ""}
                                >
                                    {next20.name} ({next20.value})
                                </span>
                            </label>
                        </div>
                    {/each}
                    {#if facetVals.hasMore}
                        <span>More values not displayed...</span>
                    {/if}
                </div>
            </details>
        </div>
    {/if}
</div>
