  <!-- JavaScript -->
  <script src="{_FEJS_}/jQuery.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"
  	integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA=="
  	crossorigin=""></script>
  <script src="{_FEJS_}/vendors.js"></script>
  <script src="{_FEJS_}/main.js"></script>


  <script src="{_FEJS_}/scripting/website.js"></script>
  {if isset($erreur) || isset($succes)}
		{include file="{$template}/common/_notification.tpl"}
	{/if}

  </body>

</html>