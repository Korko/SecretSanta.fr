function addParticipant () {

	var el = document.getElementsByClassName("participant")[0].cloneNode(true);
	var inputs = el.getElementsByTagName("input");

	for (i in inputs) {
		inputs[i].value = "";
	}

	document.getElementById("participants").appendChild(el);

}

function removeParticipant (participant) {

	var index = Array.prototype.slice.call(document.getElementsByClassName("participant")).indexOf(participant);
	participant.parentNode.removeChild(participant);

	var parts = document.getElementsByClassName("participant"), select;
	for (var i = 0; i < parts.length; i++) {
		select = parts[i].getElementsByTagName("select")[0];
		if (parseInt(select.options[select.selectedIndex].value, 10) === index) {
			select.selectedIndex = 0;
		}

		for (var j = 0; j < select.options.length; j++) {
			if (parseInt(select.options[j].value, 10) === index) {
				select.removeChild(select.options[j]);
				break;
			}
		}
	}

}

function updateParticipant (participant) {

	var index = Array.prototype.slice.call(document.getElementsByClassName("participant")).indexOf(participant);
	var parts = document.getElementsByClassName("participant"), select, option, founded;
	for (var i = 0; i < parts.length; i++) {
		if (i === index) continue;

		select = parts[i].getElementsByTagName("select")[0];
		founded = false;

		for (var j = 0; j < select.options.length; j++) {
			if (parseInt(select.options[j].value, 10) === index) {
				select.options[j].text = participant.getElementsByTagName("input")[0].value;
				founded = true;
			}
		}

		if (!founded) {
			option = document.createElement("option");
			option.value = index;
			option.text = participant.getElementsByTagName("input")[0].value;
			select.appendChild(option);
		}
	}

}

function updatePartner (participant) {

}
