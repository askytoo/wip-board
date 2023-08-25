<script lang="ts">
    import { page } from "@inertiajs/svelte";
    import type { Task } from "@/types/task";

    import {
        createSvelteTable,
        flexRender,
        getCoreRowModel,
        getSortedRowModel,
        getFilteredRowModel,
        getFacetedRowModel,
        getFacetedUniqueValues,
        getFacetedMinMaxValues,
        getPaginationRowModel,
        getExpandedRowModel,
        type SortDirection,
        type FilterFn,
        type ExpandedState,
        type VisibilityState,
    } from "@tanstack/svelte-table";
    import { rankItem } from "@tanstack/match-sorter-utils";
    import { writable } from "svelte/store";
    import type { ColumnDef, TableOptions } from "@tanstack/svelte-table";
    import FacetCheckboxes from "@/Components/Tasks/FacetCheckboxes.svelte";
    import TodayTaskCheckBox from "@/Components/Tasks/TodayTaskCheckBox.svelte";

    import TextBoxEdit from "svelte-material-icons/TextBoxEdit.svelte";
    import Delete from "svelte-material-icons/Delete.svelte";
    import ContentCopy from "svelte-material-icons/ContentCopy.svelte";
    import FilterOutline from "svelte-material-icons/FilterOutline.svelte";
    import Magnify from "svelte-material-icons/Magnify.svelte";

    let defaultData: Task[] = $page.props.tasks;

    const numFormat = new Intl.NumberFormat("de-DE", {
        style: "currency",
        currency: "EUR",
    });

    function getSortSymbol(isSorted: boolean | SortDirection) {
        return isSorted ? (isSorted === "asc" ? "🔼" : "🔽") : "";
    }

    const getExpandSymbol = (isExpanded: boolean) => (isExpanded ? "▼" : "▶️");

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
            accessorKey: "deadline.date",
            header: () => "期限日",
            id: "deadline_date",
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

    let expanded: ExpandedState = {};

    const setExpanded = (updater) => {
        if (updater instanceof Function) {
            expanded = updater(expanded);
        } else {
            expanded = updater;
        }
        options.update((old) => {
            return {
                ...old,
                state: {
                    ...old.state,
                    expanded,
                },
            };
        });
    };

    let columnVisibility: VisibilityState = { deadline_date: false };

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
        getExpandedRowModel: getExpandedRowModel(),
        onExpandedChange: setExpanded,
        state: {
            globalFilter,
            pagination: {
                pageSize: 7,
                pageIndex: 0,
            },
            expanded,
            columnVisibility,
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

    import { copyingTask } from "../../stores";

    export let copying = false;

    const handleClickCopyingButton = (task: Task) => {
        copyingTask.set(task);
        copying = true;
    };
</script>

<div class="px-4 text-gray-300">
    <div class="flex items-center">
        <FilterOutline
            class="dark:text-gray-300 text-gray-700 inline-block align-middle mr-2"
            size={"2rem"}
            title={"フィルター"}
        />
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
                {/if}
            {/each}
        {/each}
    </div>
    <div class="flex items-center mt-3">
        <Magnify
            class="dark:text-gray-300 text-gray-700 inline-block align-middle mr-2"
            size={"2rem"}
            title={"検索"}
        />
        <input
            {...noTypeCheck(null)}
            type="search"
            class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm mt-1 block w-full"
            on:keyup={handleSearch}
            on:search={handleSearch}
            placeholder="Search..."
        />
    </div>
    <table class="border-collapse w-full mt-3">
        <thead>
            {#each headerGroups as headerGroup}
                <tr
                    class="border-b-2 border-gray-700 dark:border-gray-300 text-center"
                >
                    <button
                        class="py-2 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                        on:click={() => $table.toggleAllRowsExpanded()}
                    >
                        {getExpandSymbol($table.getIsAllRowsExpanded())}
                    </button>
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
                                            header.column.columnDef.header,
                                            header.getContext()
                                        )}
                                    />
                                    <span class="pl-1">
                                        {getSortSymbol(
                                            header.column
                                                .getIsSorted()
                                                .toString()
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
                <tr class="border-b border-gray-500">
                    <td class="py-5 px-2 text-center">
                        <button on:click={() => row.toggleExpanded()}>
                            {getExpandSymbol(row.getIsExpanded())}
                        </button>
                    </td>
                    {#each row.getVisibleCells() as cell}
                        <td class="py-5 px-2 text-center">
                            {#if cell.column.id === "is_today_task"}
                                <TodayTaskCheckBox
                                    task={cell.getContext().row.original}
                                    isTodayTask={cell.getValue()}
                                />
                            {:else}
                                <svelte:component
                                    this={flexRender(
                                        cell.column.columnDef.cell,
                                        cell.getContext()
                                    )}
                                />
                            {/if}
                        </td>
                    {/each}
                    <td class="py-5 px-2 text-center">
                        <button
                            on:click={() =>
                                handleClickEditingButton(row.original)}
                        >
                            <TextBoxEdit
                                class="dark:text-gray-300 text-gray-700 hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"編集"}
                            />
                        </button>
                        <button
                            on:click={() =>
                                handleClickDeletingButton(row.original)}
                            class="ml-2"
                        >
                            <Delete
                                class="dark:text-gray-300 text-gray-700 hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"削除"}
                            />
                        </button>
                        <button
                            on:click={() =>
                                handleClickCopyingButton(row.original)}
                            class="ml-2"
                        >
                            <ContentCopy
                                class="dark:text-gray-300 text-gray-700 hover:text-indigo-400"
                                size={"1.5rem"}
                                title={"コピー"}
                            />
                        </button>
                    </td>
                </tr>
                <!-- 各行の詳細を表示する -->
                {#if row.getIsExpanded()}
                    <tr
                        class="border-b border-gray-500 dark:text-gray-400 text-gray-600"
                    >
                        <td />
                        <td
                            colspan={row.getVisibleCells().length + 3}
                            class="py-2 px-2"
                        >
                            <!-- ここに展開中のコンテンツを追加 -->
                            <div>
                                詳細: {row.original.description ?? "なし"}
                            </div>
                            <div class="flex pt-1">
                                <div>
                                    作成日: {row.original.created_at}
                                </div>
                                <div class="pl-5">
                                    着手日: {row.original.started_at === ""
                                        ? "未着手"
                                        : row.original.started_at}
                                </div>
                                <div
                                    class="pl-5 text-{row.original.status
                                        .class}"
                                >
                                    完了日 : {row.original.completed_at === ""
                                        ? "未完了"
                                        : formatDate()}
                                </div>
                            </div>
                        </td>
                    </tr>
                {/if}
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
                setCurrentPage($table.getState().pagination.pageIndex - 1)}
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
                setCurrentPage($table.getState().pagination.pageIndex + 1)}
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
        <span>{$table.getPrePaginationRowModel().rows.length} total Rows</span>
    </div>
</div>
