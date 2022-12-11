{if $get.module eq '404' or $get.action eq 'changementmotdepasse' or $get.action eq 'changementmotdepasseoublie'}

    {include file="{$template}/modules/{$get.module}/{$get.action}.tpl"}

{else}
    {if isset($get.action) && ($get.action neq 'imprimer')}
        {include file="{$template}/common/_header.tpl"}

        {include file="{$template}/modules/{$get.module}/{$get.action}.tpl"}

        {if $get.module neq 'connexion'}
            {*include file="{$template}/common/_pre_footer.tpl"*}
        {/if}

        {include file="{$template}/common/_footer.tpl"}

    {/if}
{/if}