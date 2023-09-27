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
    } from "@tanstack/svelte-table";
    import { rankItem } from "@tanstack/match-sorter-utils";
    import { writable } from "svelte/store";
    import type {
        ColumnDef,
        FilterFnOption,
        SortingState,
        TableOptions,
    } from "@tanstack/svelte-table";
    import FacetCheckboxes from "@/Components/Tasks/FacetCheckboxes.svelte";
    import InputFilterDate from "./InputFilterDate.svelte";
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

    /**
     * 日付のフィルタ用関数
     */
    const dateFilter: FilterFnOption<Task> = (
        row,
        columnId,
        filterValue: [Date | null, Date | null]
    ) => {
        const isAfter = (date: Date, dateToCompare: Date | number) => {
            return date.getTime() > new Date(dateToCompare).getTime();
        };

        const isBefore = (date: Date, dateToCompare: Date | number) => {
            return date.getTime() < new Date(dateToCompare).getTime();
        };

        const isEqual = (date: Date, dateToCompare: Date | number) => {
            return date.getTime() === new Date(dateToCompare).getTime();
        };

        const isWithinInterval = (date: Date, interval: Interval) => {
            return (
                isAfter(date, interval.start) &&
                isBefore(date, interval.end) &&
                isEqual(date, interval.start) === false &&
                isEqual(date, interval.end) === false
            );
        };

        const rowValue = row.getValue(columnId);
        const rowValueDate = new Date(rowValue as string);

        const filterStartDate = filterValue[0];
        const filterEndDate = filterValue[1];

        if (filterStartDate && filterEndDate) {
            // 期間の開始・終了が指定されている場合
            const isValid = isWithinInterval(rowValueDate, {
                start: filterStartDate,
                end: filterEndDate,
            });
            return isValid;
        } else if (filterStartDate && !filterEndDate) {
            // 期間の開始が指定されている場合
            const isValid =
                isEqual(rowValueDate, filterStartDate) ||
                isAfter(rowValueDate, filterStartDate);
            return isValid;
        } else if (!filterStartDate && filterEndDate) {
            // 期間の終了が指定されている場合
            const isValid =
                isEqual(rowValueDate, filterEndDate) ||
                isBefore(rowValueDate, filterEndDate);
            return isValid;
        } else {
            // 期間が指定されていない場合
            return true;
        }
    };

    const defaultColumns: ColumnDef<Task>[] = [
        {
            accessorKey: "status.label",
            header: "ステータス",
            id: "status",
            cell: (info) => info.getValue() as string,
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "deadline.full",
            header: () => "期日",
            id: "deadline",
            cell: (info) => info.getValue() as string,
            filterFn: dateFilter,
        },
        {
            accessorKey: "title",
            header: () => "タイトル",
            cell: (info) => info.getValue() as string,
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "estimated_effort",
            header: () => "見積作業時間(分)",
            cell: (info) => info.getValue() as number,
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "output",
            header: () => "アウトプット",
            cell: (info) => info.getValue() as string,
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "is_today_task",
            header: () => "今日のタスク",
            id: "is_today_task",
            cell: (info) => info.getValue() as boolean,
            filterFn: globalFilterFn,
        },
        {
            accessorKey: "created_at",
            header: () => "作成日",
            id: "created_at",
            cell: (info) => info.getValue() as string,
            filterFn: dateFilter,
        },
        {
            accessorFn: (originalRow) => {
                const row = originalRow as Task;
                return getStartedAt(row);
            },
            header: () => "着手日",
            id: "started_at",
            cell: (info) => info.getValue() as string,
            filterFn: dateFilter,
        },
        {
            accessorFn: (originalRow) => {
                const row = originalRow as Task;
                return getCompletedAt(row);
            },
            header: () => "完了日",
            id: "completed_at",
            cell: (info) => info.getValue() as string,
            filterFn: dateFilter,
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

    let sorting: SortingState = [];

    const setSorting = (updater) => {
        if (updater instanceof Function) {
            sorting = updater(sorting);
        } else {
            sorting = updater;
        }
        options.update((old) => ({
            ...old,
            state: {
                ...old.state,
                sorting,
            },
        }));
    };

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
        onSortingChange: setSorting,
        state: {
            globalFilter,
            pagination: {
                pageSize: 7,
                pageIndex: 0,
            },
            expanded,
            sorting,
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

    import getStartedAt from "@/utils/getStartedAtFromTask";
    import getCompletedAt from "@/utils/getCompletedAtFromTask";
    import getActualTaskEffort from "@/utils/getActualTaskEffort";

    // filter用のカラムは表示しないようにする
    const columnUnvisibilities = ["created_at", "started_at", "completed_at"];
</script>

<div class="px-4 text-gray-300 h-full overflow-y-auto hidden-scrollbar pb-44">
    <div class="flex">
        <FilterOutline
            class="dark:text-gray-300 text-gray-700 inline-block align-middle mr-2"
            size={"2rem"}
            title={"フィルター"}
        />
        {#each headerGroups as headerGroup}
            {#each headerGroup.headers as header}
                {#if header.column.id === "status"}
                    <details open={true}>
                        <summary
                            class="hover:text-indigo-400 hover:cursor-pointer"
                        >
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
                    <details open={true} class="pl-8">
                        <summary
                            class="hover:text-indigo-400 hover:cursor-pointer"
                        >
                            <h3 class="font-semibold inline-block">期日</h3>
                        </summary>
                        <InputFilterDate
                            table={$table}
                            column={header.column}
                        />
                    </details>
                {:else if header.column.id === "created_at"}
                    <details open={true} class="pl-4">
                        <summary
                            class="hover:text-indigo-400 hover:cursor-pointer"
                        >
                            <h3 class="font-semibold inline-block">作成日</h3>
                        </summary>
                        <InputFilterDate
                            table={$table}
                            column={header.column}
                        />
                    </details>
                {:else if header.column.id === "started_at"}
                    <details open={true} class="pl-4">
                        <summary
                            class="hover:text-indigo-400 hover:cursor-pointer"
                        >
                            <h3 class="font-semibold inline-block">着手日</h3>
                        </summary>
                        <InputFilterDate
                            table={$table}
                            column={header.column}
                        />
                    </details>
                {:else if header.column.id === "completed_at"}
                    <details open={true} class="pl-4">
                        <summary
                            class="hover:text-indigo-400 hover:cursor-pointer"
                        >
                            <h3 class="font-semibold inline-block">完了日</h3>
                        </summary>
                        <InputFilterDate
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
                        class="py-5 px-3 transition-colors ease-in-out hover:text-indigo-400 disabled:text-gray-600"
                        on:click={() => $table.toggleAllRowsExpanded()}
                    >
                        {getExpandSymbol($table.getIsAllRowsExpanded())}
                    </button>
                    {#each headerGroup.headers as header}
                        {#if !columnUnvisibilities.includes(header.column.id)}
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
                                        <!-- <span class="pl-1"> -->
                                        <!--     {getSortSymbol( -->
                                        <!--         header.column -->
                                        <!--             .getIsSorted() -->
                                        <!--             .toString() -->
                                        <!--     )} -->
                                        <!-- </span> -->
                                    </button>
                                {/if}
                            </th>
                        {/if}
                    {/each}
                </tr>
            {/each}
        </thead>

        <tbody class="h-full">
            {#each $table.getRowModel().rows as row}
                <tr class="border-b border-gray-500">
                    <td class="py-5 px-2 text-center">
                        <button on:click={() => row.toggleExpanded()}>
                            {getExpandSymbol(row.getIsExpanded())}
                        </button>
                    </td>
                    {#each row.getVisibleCells() as cell}
                        {#if !columnUnvisibilities.includes(cell.column.id)}
                            <td
                                class="py-5 px-2 max-w-lg text-center whitespace-normal overflow-hidden"
                            >
                                {#if cell.column.id === "is_today_task"}
                                    <TodayTaskCheckBox
                                        task={cell.getContext().row.original}
                                        isTodayTask={cell.getValue()}
                                    />
                                {:else}
                                    <svelte:component
                                        this={flexRender(
                                            cell.column.id === "deadline"
                                                ? new Date(
                                                      cell.getValue()
                                                  ).toLocaleDateString(
                                                      "ja-JP",
                                                      {
                                                          year: "numeric",
                                                          month: "2-digit",
                                                          day: "2-digit",
                                                          hour: "2-digit",
                                                          minute: "2-digit",
                                                      }
                                                  )
                                                : cell.column.columnDef.cell,
                                            cell.getContext()
                                        )}
                                    />
                                {/if}
                            </td>
                        {/if}
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
                                title={"複製"}
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
                                    着手日: {getStartedAt(row.original)}
                                </div>
                                <div class="pl-5">
                                    完了日 : {getCompletedAt(row.original)}
                                </div>
                                <div class="pl-5">
                                    実作業時間 : {getActualTaskEffort(row.original)}分
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
<div class="ripple-container">
    <button class="ripple text-white" on:click={() => console.log("click")}>
        Click me
    </button>
</div>

<style>
    .ripple-container {
        position: relative;
        overflow: hidden;
    }

    .ripple {
        position: absolute;
        border-radius: 50%;
        background-color: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
</style>
