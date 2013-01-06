<script>
document.observe('dom:loaded', function(evt) {
	var select, selects, _i, _len, _results;
	if (Prototype.Browser.IE && (Prototype.BrowserFeatures['Version'] === 6 || Prototype.BrowserFeatures['Version'] === 7)) {
		return;
	}
	selects = $$(".<?php echo $class; ?>");
	_results = [];
	for (_i = 0, _len = selects.length; _i < _len; _i++) {
		select = selects[_i];
		_results.push(new Chosen(select));
	}
	deselects = $$(".<?php echo $class; ?>-deselect");
	for (_i = 0, _len = deselects.length; _i < _len; _i++) {
		select = deselects[_i];
		_results.push(new Chosen(select,{allow_single_deselect:true}));
	}
	return _results;
});
</script>