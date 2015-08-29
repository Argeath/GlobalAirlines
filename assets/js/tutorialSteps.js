$(function() {
	"use strict";
	
	var steps = [];
	
	function TutorialStep(id)
	{
		this.id = id;
		this.elements = [];
		this.load = null;
		return this;
	}
	
	//Step 1
	{
		var step = new TutorialStep(1);
		var e1 = tutorialElement($('#tutorial_odloty'), "Hard_Bottom_Left", "Site_Bottom", "Odprawy oraz zaplanowane odloty", "inner", false);
	}

});