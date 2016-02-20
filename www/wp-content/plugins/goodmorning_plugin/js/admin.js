jQuery(document).ready(function(){
	var hero_meta = new heroMetaCtrl();
});

var heroMetaCtrl = function(){
	var $ = jQuery;

	this.numRows = 0;

	this.init = function(){

		$("ul.layers-list").sortable();

		this.numRows = $("#hero_layers li.layer").length;

		var that = this;

		$("#hero_layers").on("click", "li.layer a.add", function(e){
			e.preventDefault();
			that.addRow();
		});

		$("#hero_layers").on("click", "li.layer a.remove", function(e){
			e.preventDefault();

			$row = $(e.target).parents("li.layer");

			that.removeRow($row);
		});

	}

	this.addRow = function(){
		var $row = $("#hero_layers li.layer-master").clone();
		this.numRows++

		$row.removeClass("layer-master").addClass("layer");
		$row.html( $row.html().replace(new RegExp("%num%", 'g'), this.numRows) );
		$row.attr("id", "layer-" + this.numRows);

		$("#hero_layers ul.layers-list").append($row);

	}

	this.removeRow = function($row){
		if(this.numRows <= 1){
			return;
		}

		$row.remove();
	}

	this.init();

}