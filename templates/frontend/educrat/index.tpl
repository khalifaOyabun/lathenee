{if $get.module eq '404' or $get.action eq 'changementmotdepasse' or $get.action eq 'changementmotdepasseoublie'}

    {include file="{$template}/modules/{$get.module}/{$get.action}.tpl"}
{else if $get.module eq 'postuler'}
    {include file="{$template}/common/_header.tpl"}

    {include file="{$template}/common/_subheader.tpl"}

    {include file="{$template}/modules/{$get.module}/{$get.action}.tpl"}
    
    {include file="{$template}/common/_footer.tpl"}

{else}
    {include file="{$template}/common/_header.tpl"}

    {include file="{$template}/common/_subheader.tpl"}

    {include file="{$template}/modules/{$get.module}/{$get.action}.tpl"}

    {include file="{$template}/common/_supfooter.tpl"}

    {include file="{$template}/common/_footer.tpl"}
{/if}