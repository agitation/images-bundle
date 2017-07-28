ag.ns("ag.image");

(function(){

var DescriptionModal = function(desc, submitCallback)
{
    ag.ui.modal.Dialog.call(this);

    var field = new ag.mlang.field.Multilang();

    this.find(".header").html(ag.ui.tool.tpl("agit-images-admin-field", ".modal-header"));

    this.find(".footer").html([
        this.createButton("cancel"),
        this.createButton("ok")
    ]);

    this.form
        .submit(() => submitCallback(field.getValue()))
        .find(".body").html(field);

    field.setValue(desc).focus();
};

DescriptionModal.prototype = Object.create(ag.ui.modal.Dialog.prototype);

ag.image.DescriptionModal = DescriptionModal;

})();
