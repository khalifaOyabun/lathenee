{*<div class="floating-alert depth-10">
{if isset($erreur)}
<div class="alert alert-danger fade show" role="alert">
<div class="alert-icon"><i class="flaticon-warning"></i></div>
<div class="alert-text">{$erreur}</div>
<div class="alert-close">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true"><i class="la la-close"></i></span>
</button>
</div>
</div>
{/if}
{if isset($succes)}
<div class="alert alert-success fade show" role="alert">
<div class="alert-icon"><i class="flaticon2-correct"></i></div>
<div class="alert-text">{$succes}</div>
<div class="alert-close">
<button type="button" class="close" data-dismiss="alert" aria-label="Close">
<span aria-hidden="true"><i class="la la-close"></i></span>
</button>
</div>
</div>
{/if}
</div>*}
<script>
    {if isset($erreur)}
    notify(-1, "{$erreur}")
    {/if}
    {if isset($succes)}
    notify(1, "{$succes}")
    {/if}
</script>