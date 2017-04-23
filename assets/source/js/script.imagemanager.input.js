var imageManagerInput = {
	baseUrl: null,
	//language
	message: null,
	//init imageManagerInput
	init: function(){
		//create modal
		imageManagerInput.initModal();
	},
	//creat image Manager modal
	initModal: function(){
		//check if modal not jet exists
		if($("#modal-imagemanager").length === 0){
			//set html modal in var
			var sModalHtml = '<div tabindex="-1" role="dialog" class="fade modal" id="modal-imagemanager">';
				sModalHtml += '<div class="modal-dialog modal-lg">';
					sModalHtml += '<div class="modal-content">';
						sModalHtml += '<div class="modal-header">';
							sModalHtml += '<button aria-hidden="true" data-dismiss="modal" class="close" type="button">&times;</button>';
							sModalHtml += '<h4>Image manager</h4>';
						sModalHtml += '</div>';
						sModalHtml += '<div class="modal-body">';
							sModalHtml += '<iframe src="#"></iframe>';
						sModalHtml += '</div>';
					sModalHtml += '</div>';
				sModalHtml += '</div>';
			sModalHtml += '</div>';
			//prepend data to body
			$('body').prepend(sModalHtml);
		}
	},
	//open media manager modal
	openModal: function(inputId, aspectRatio, cropViewMode){
		//get selected item
		var iImageId = $("#"+inputId).val();
		var srcImageIdQueryString = "";
		if(iImageId !== ""){
			srcImageIdQueryString = "&image-id="+iImageId;
		}
		//create iframe url
		var queryStringStartCharacter = ((imageManagerInput.baseUrl).indexOf('?') == -1) ? '?' : '&';
		var imageManagerUrl = imageManagerInput.baseUrl+queryStringStartCharacter+"view-mode=iframe&input-id="+inputId+"&aspect-ratio="+aspectRatio+"&crop-view-mode="+cropViewMode+srcImageIdQueryString;
		//set iframe path
		$("#modal-imagemanager iframe").attr("src",imageManagerUrl);
                //set translation title for modal header
                $("#modal-imagemanager .modal-dialog .modal-header h4").text(imageManagerInput.message.imageManager); 
		//open modal
		$("#modal-imagemanager").modal("show");
	},
	//close media manager modal
	closeModal: function(){
		$("#modal-imagemanager").modal("hide");
	},
	//delete picked image
	deletePickedImage: function(inputId){
		//remove value of the input field
		var sFieldId = inputId;
		var sFieldNameId = sFieldId+"_name";
		var sImagePreviewId = sFieldId+"_image";
		var bShowConfirm = JSON.parse($(".delete-selected-image[data-input-id='"+inputId+"']").data("show-delete-confirm"));
		//show warning if bShowConfirm == true
		if(bShowConfirm){
			if(confirm(imageManagerInput.message.detachWarningMessage) == false){
				return false;
			}
		}		
		//set input data		
		$('#'+sFieldId).val("");
		$('#'+sFieldNameId).val("");
		//trigger change
		$('#'+sFieldId).trigger("change");
		//hide image
		$('#'+sImagePreviewId).attr("src","").parent().addClass("hide");	
		//delete hide class
		$(".delete-selected-image[data-input-id='"+inputId+"']").addClass("hide");
	}
};

$(document).ready(function () {
	//init Image manage
	imageManagerInput.init();
	
	//open media manager modal
	$(document).on("click", ".open-modal-imagemanager", function () {
		var aspectRatio = $(this).data("aspect-ratio");
		var cropViewMode = $(this).data("crop-view-mode");
		var inputId = $(this).data("input-id");
		//open selector id
		imageManagerInput.openModal(inputId, aspectRatio, cropViewMode);
	});	
	
	//delete picked image
	$(document).on("click", ".delete-selected-image", function () {
		var inputId = $(this).data("input-id");
		//open selector id
		imageManagerInput.deletePickedImage(inputId);
	});	
});