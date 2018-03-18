ag.ns("ag.admin.field");

(function(){

var Img = function(options)
{
    this.extend(this, ag.u.tpl("ag-admin-img", ".image-field"));
    this.opts = options || {};

    this.img = this.find("img");

    this.input = this.find("input").change(() => {
        this.input[0].files.length && readFile.call(this, this.input[0].files[0]);
    });

    this.find(".clear").click(() => removeImage.call(this));

    var handler = this.find(".handler");

    handler.click(() => {
        ag.s.msg.clear(); // avoid confusion by stale messages
        this.input.trigger("click");
    });

    handler.on('drop', event => {
        var ev = event.originalEvent;

        ev.preventDefault();

        if (ev.dataTransfer.items.length && ev.dataTransfer.items[0].kind === "file")
        {
            readFile.call(this, ev.dataTransfer.items[0].getAsFile());
        }
    });

    handler.on('dragover', ev => ev.preventDefault());
},

readFile = function(file)
{
    if (file.type.indexOf("image/") === 0)
    {
        var fileReader = new FileReader();
        fileReader.onload = (ev) => this.setValue(ev.target.result);
        fileReader.readAsDataURL(file);
    }
},

removeImage = function()
{
        delete(this.image);
        this.img.attr("src", "");
        this.removeClass("has-image");
},

updatePreview = function()
{
    var width = this.image.width,
        height = this.image.height;

    try
    {
        if (this.opts.minWidth && width < this.opts.minWidth)
            throw ag.u.sprintf(ag.intl.t("The image must be at least %s pixels wide."), this.opts.minWidth);

        if (this.opts.maxWidth && width > this.opts.maxWidth)
            throw ag.u.sprintf(ag.intl.t("The image must be at most %s pixels wide."), this.opts.maxWidth);

        if (this.opts.minHeight && height < this.opts.minHeight)
            throw ag.u.sprintf(ag.intl.t("The image must be at least %s pixels high."), this.opts.minHeight);

        if (this.opts.maxHeight && height > this.opts.maxHeight)
            throw ag.u.sprintf(ag.intl.t("The image must be at most %s pixels high."), this.opts.maxHeight);

        this.img.attr("src", this.image.src);
    }
    catch (e)
    {
        removeImage.call(this);
        ag.s.msg.alert(e);
    }
};

Img.prototype = Object.create(ag.ui.field.ComplexField.prototype);

Img.prototype.setValue = function(value)
{
    removeImage.call(this);

    if (value)
    {
        this.image = new Image();
        this.image.src = value;
        this.image.addEventListener("load", () => updatePreview.call(this));
        this.addClass("has-image");
    }

    this.triggerHandler("ag.field.set");
    return this;
};

Img.prototype.getValue = function()
{
    return this.image ? this.image.src : null;
};

ag.admin.field.Image = Img;

})();
