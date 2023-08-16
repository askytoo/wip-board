<script lang="ts">
  let count:number = 0;
</script>

<!-- 減算するボタン -->
<button on:click={() => (count -= 1)} aria-label="減算">-</button>
<!-- 定義したcount変数 -->
{count}
<!-- 加算するボタン -->
<button on:click={() => (count += 1)} aria-label="加算">+</button>
