<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import type { Task } from "@/types/task";

    import {
        createSvelteTable,
        createColumnHelper,
        flexRender,
        getCoreRowModel,
        getSortedRowModel,
        getFilteredRowModel,
        getFacetedRowModel,
        getFacetedUniqueValues,
        getFacetedMinMaxValues,
        getPaginationRowModel,
        type SortDirection,
        type FilterFn,
    } from "@tanstack/svelte-table";
    import { rankItem } from "@tanstack/match-sorter-utils";
    import { writable } from "svelte/store";
    import type { ColumnDef, TableOptions } from "@tanstack/svelte-table";
    import FacetCheckboxes from "@/Components/Tasks/FacetCheckboxes.svelte";
    import FacetMinMax from "@/Components/Tasks/FacetMinMax.svelte";

    let defaultData: Task[] = $page.props.tasks;

    const numFormat = new Intl.NumberFormat("de-DE", {
        style: "currency",
        currency: "EUR",
    });

    function getSortSymbol(isSorted: boolean | SortDirection) {
        return isSorted ? (isSorted === "asc" ? "🔼" : "🔽") : "";
    }

    const globalFilterFn: FilterFn<any> = (row, columnId, value, addMeta) => {
        if (Array.isArray(value)) {
            if (value.length === 0) return true;
            return value.includes(row.getValue(columnId));
        }

        // Rank the item
        const itemRank = rankItem(row.getValue(columnId), value);

        // Store the itemRank info
        addMeta({
            itemRank,
        });

        // Return if the item should be filtered in/out
        return itemRank.passed;
    };

    const columnHelper = createColumnHelper<Task>();

    const defaultColumns: ColumnDef<Task>[] = [
        {
            accessorKey: "status.label",
            header: "ステータス",
            id: "status",
            cell: (info) => info.getValue(),
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "deadline.full",
            header: () => "期日",
            id: "deadline",
            cell: (info) => info.getValue(),
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "title",
            header: () => "タイトル",
            cell: (info) => info.getValue(),
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "estimated_effort",
            header: () => "見積作業時間(分)",
            cell: (info) => (info.getValue() as number).toString(),
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "output",
            header: () => "アウトプット",
            cell: (info) => info.getValue(),
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "is_today_task.label",
            header: () => "今日のタスク",
            id: "is_today_task",
            cell: (info) => info.getValue(),
            filterFn: globalFilterFn,
        },
    ];

    let globalFilter = "";

    const options = writable<TableOptions<Task>>({
        data: defaultData,
        columns: defaultColumns,
        getCoreRowModel: getCoreRowModel(),
        getSortedRowModel: getSortedRowModel(),
        getFilteredRowModel: getFilteredRowModel(),
        globalFilterFn: globalFilterFn,
        getFacetedRowModel: getFacetedRowModel(),
        getFacetedUniqueValues: getFacetedUniqueValues(),
        getFacetedMinMaxValues: getFacetedMinMaxValues(),
        getPaginationRowModel: getPaginationRowModel(),
        state: {
            globalFilter,
            pagination: {
                pageSize: 7,
                pageIndex: 0,
            },
        },
        enableGlobalFilter: true,
    });

    const table = createSvelteTable(options);

    const rerender = () => {
        options.update((options) => ({
            ...options,
            data: $page.props.tasks,
        }));
    };

    page.subscribe(() => {
        rerender();
    });

    function setGlobalFilter(filter: string) {
        globalFilter = filter;
        options.update((old) => {
            return {
                ...old,
                state: {
                    ...old.state,
                    globalFilter: filter,
                },
            };
        });
    }

    function setCurrentPage(page: number) {
        options.update((old: any) => {
            return {
                ...old,
                state: {
                    ...old.state,
                    pagination: {
                        ...old.state?.pagination,
                        pageIndex: page,
                    },
                },
            };
        });
    }

    function setPageSize(e: Event) {
        const target = e.target as HTMLInputElement;
        options.update((old: any) => {
            return {
                ...old,
                state: {
                    ...old.state,
                    pagination: {
                        ...old.state?.pagination,
                        pageSize: parseInt(target.value),
                    },
                },
            };
        });
    }

    let timer: NodeJS.Timeout;
    function handleSearch(e: Event) {
        clearTimeout(timer);
        timer = setTimeout(() => {
            const target = e.target as HTMLInputElement;
            setGlobalFilter(target.value);
        }, 300);
    }

    function handleCurrPageInput(e: Event) {
        const target = e.target as HTMLInputElement;
        setCurrentPage(parseInt(target.value) - 1);
    }

    const noTypeCheck = (x: any) => x;

    let headerGroups = $table.getHeaderGroups();

    import { editingTask } from "../../stores";

    export let editing = false;

    const handleClickEditingButton = (task: Task) => {
        editingTask.set(task);
        editing = true;
    };

    import { deletingTask } from "../../stores";

    export let deleting = false;

    const handleClickDeletingButton = (task: Task) => {
        deletingTask.set(task);
        deleting = true;
    };
</script>

<div class="px-4 text-gray-300">
    <h1 class="text-4xl">Invoices</h1>

    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div class="md:col-span-1">
            <h2 class="text-2xl mb-3">Filters</h2>

            {#each headerGroups as headerGroup}
                {#each headerGroup.headers as header}
                    {#if header.column.id === "status"}
                        <details open>
                            <summary>
                                <h3 class="font-semibold inline-block">
                                    ステータス
                                </h3>
                            </summary>

                            <FacetCheckboxes
                                table={$table}
                                column={header.column}
                            />
                        </details>
                    {:else if header.column.id === "deadline"}
                        <details open>
                            <summary>
                                <h3 class="font-semibold inline-block">期日</h3>
                            </summary>

                            <FacetMinMax
                                table={$table}
                                column={header.column}
                            />
                        </details>
                    {/if}
                {/each}
            {/each}
        </div>
        <div class="md:col-span-4">
            <input
                {...noTypeCheck(null)}
                type="search"
                class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
                on:keyup={handleSearch}
                on:search={handleSearch}
                placeholder="Search..."
            />
            <table class="border-collapse w-full mt-3">
                <thead>
                    {#each headerGroups as headerGroup}
                        <tr
                            class="border-b-2 border-gray-700 dark:border-gray-300"
                        >
                            {#each headerGroup.headers as header}
                                <th colspan={header.colSpan}>
                                    {#if !header.isPlaceholder}
                                        <button
                                            class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                                            class:disabled={!header.column.getCanSort()}
                                            disabled={!header.column.getCanSort()}
                                            on:click={header.column.getToggleSortingHandler()}
                                        >
                                            <svelte:component
                                                this={flexRender(
                                                    header.column.columnDef
                                                        .header,
                                                    header.getContext()
                                                )}
                                            />
                                            <span class="pl-1">
                                                {getSortSymbol(
                                                    header.column.getIsSorted()
                                                )}
                                            </span>
                                        </button>
                                    {/if}
                                </th>
                            {/each}
                        </tr>
                    {/each}
                </thead>

                <tbody>
                    {#each $table.getRowModel().rows as row}
                        <tr>
                            {#each row.getVisibleCells() as cell}
                                <td
                                    class="border-b border-gray-500 py-5 px-2 text-center"
                                >
                                    <svelte:component
                                        this={flexRender(
                                            cell.column.columnDef.cell,
                                            cell.getContext()
                                        )}
                                    />
                                </td>
                            {/each}
                            <td
                                class="border-b border-gray-500 py-5 px-2 text-center"
                            >
                                <button
                                    on:click={() =>
                                        handleClickEditingButton(row.original)}
                                >
                                    edit
                                </button>
                            </td>
                            <td
                                class="border-b border-gray-500 py-5 px-2 text-center"
                            >
                                <button
                                    on:click={() =>
                                        handleClickDeletingButton(row.original)}
                                >
                                    delete
                                </button>
                            </td>
                        </tr>
                    {/each}
                </tbody>
            </table>
            <div class="flex items-center mt-2">
                <button
                    class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                    on:click={() => setCurrentPage(0)}
                    class:disabled={!$table.getCanPreviousPage()}
                    disabled={!$table.getCanPreviousPage()}
                >
                    {"<<"}
                </button>
                <button
                    class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                    on:click={() =>
                        setCurrentPage(
                            $table.getState().pagination.pageIndex - 1
                        )}
                    class:disabled={!$table.getCanPreviousPage()}
                    disabled={!$table.getCanPreviousPage()}
                >
                    {"<"}
                </button>
                <span> Page </span>
                <input
                    type="number"
                    value={$table.getState().pagination.pageIndex + 1}
                    min={0}
                    max={$table.getPageCount() - 1}
                    on:change={handleCurrPageInput}
                    class="w-16 mx-1 border1 py-1 px-2 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block"
                />
                <span>
                    {" "}of{" "}
                    {$table.getPageCount()}
                </span>
                <button
                    class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                    on:click={() =>
                        setCurrentPage(
                            $table.getState().pagination.pageIndex + 1
                        )}
                    class:disabled={!$table.getCanNextPage()}
                    disabled={!$table.getCanNextPage()}
                >
                    {">"}
                </button>
                <button
                    class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                    on:click={() => setCurrentPage($table.getPageCount() - 1)}
                    class:disabled={!$table.getCanNextPage()}
                    disabled={!$table.getCanNextPage()}
                >
                    {">>"}
                </button>
                <span class="mx-2 font-semibold">|</span>
                <select
                    value={$table.getState().pagination.pageSize}
                    on:change={setPageSize}
                    class="border p-2 pr-8 border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm block"
                >
                    {#each [7, 10, 25, 50] as pageSize}
                        <option value={pageSize}>
                            Show {pageSize}
                        </option>
                    {/each}
                </select>
                <span class="mx-2 font-semibold">|</span>
                <span
                    >{$table.getPrePaginationRowModel().rows.length} total Rows</span
                >
            </div>
        </div>
    </div>
</div>
