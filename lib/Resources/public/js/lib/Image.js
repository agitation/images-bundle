ag.ns("ag.admin.field");

(function(){

var thumbnailSize = 80,

    defaults =
    {
        maxWidth : 400,
        maxHeight : 400,
        type : "image/jpeg"
    },

    ImageField = function(options)
    {
        this.extend(this, ag.ui.tool.tpl("agitadmin-images-field", ".thumb"));

        this.options = Object.assign({}, defaults, options || {});
        this.image = new Image();
        this.origImage = new Image();
        this.description = "";

        var tCanvas = this.find(".preview")[0],
            currentRotation = 0;

        // triggers when this.origImage.src is updated (setValue)
        this.origImage.addEventListener("load", () => {
            this.image.src = resizeCanvas(
                    imageToCanvas(this.origImage),
                    this.options.maxWidth,
                    this.options.maxHeight).toDataURL(this.options.type);
        });

        // triggers when this.image.src is updated (setValue, resizing, rotating)
        this.image.addEventListener("load", () => {
            updateThumbnail(this.image, tCanvas)
            this.removeClass("processing");
        });

        this.find(".enlarge").click(ev => {
            if (!this.is(".processing"))
            {
                ev.stopPropagation();
                showPreview(this.image);
            }
        });

        this.find(".rotate").click(ev => {
            if (!this.is(".processing"))
            {
                this.addClass("processing");
                ev.stopPropagation();

                currentRotation = ++currentRotation % 4;

                this.image.src = resizeCanvas(
                    rotateCanvas(
                        imageToCanvas(this.origImage),
                        currentRotation
                    ),
                    this.options.maxWidth,
                    this.options.maxHeight
                ).toDataURL(this.options.type);
            }
        });

        this.find(".remove").click(ev => {
            if (!this.is(".processing"))
            {
                ev.stopPropagation();
                this.trigger("ag.img.remove");
                this.remove();
            }
        });
    },

    imageToCanvas = function(image)
    {
        var vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = image.width;
        vCanvas.height = image.height;

        vCanvasCtx.drawImage(image, 0, 0);

        return vCanvas;
    },

    showPreview = function(image)
    {
        var elem = ag.ui.tool.tpl("agitadmin-images-field", ".large-image"),
            modal = new ag.ui.modal.Display();

        elem.find("img").attr("src", image.src);
        modal.addCloseButton();
        modal.setContent(elem);
        modal.appear();
    },

    updateThumbnail = function(image, tCanvas)
    {
        var tFactor = Math.max(image.width / thumbnailSize, image.height / thumbnailSize),
            tContext = tCanvas.getContext("2d");
            tWidth = image.width / tFactor,
            tHeight = image.height / tFactor;

        tContext.clearRect(0, 0, tCanvas.width, tCanvas.height);
        tContext.drawImage(image, (thumbnailSize - tWidth) / 2, (thumbnailSize - tHeight) / 2, tWidth, tHeight);
    },

    // NB: returns a modified clone of the canvas
    resizeCanvas = function(canvas, maxWidth, maxHeight)
    {
        var resizeFactor = Math.max(canvas.width / maxWidth, canvas.height / maxHeight),
            vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = canvas.width;
        vCanvas.height = canvas.height;

        vCanvasCtx.drawImage(canvas, 0, 0);

        if (resizeFactor > 1)
            hermiteResize(vCanvas, canvas.width / resizeFactor, canvas.height / resizeFactor);

        return vCanvas;
    },

    // NB: returns a modified clone of the canvas
    rotateCanvas = function(canvas, steps)
    {
        var vCanvas = document.createElement("canvas"),
            vCanvasCtx = vCanvas.getContext("2d");

        vCanvas.width = canvas.width;
        vCanvas.height = canvas.height;

        vCanvasCtx.save();

        if (steps % 2)
        {
            vCanvas.width = canvas.height;
            vCanvas.height = canvas.width;
        }

        vCanvasCtx.translate(vCanvas.width / 2, vCanvas.height / 2);

        if (steps)
        {
            vCanvasCtx.rotate(Math.PI / 2 * steps);
        }

        vCanvasCtx.drawImage(canvas, -(canvas.width / 2), -(canvas.height / 2));
        vCanvasCtx.restore();

        return vCanvas;
    };

ImageField.prototype = Object.create(jQuery.prototype);

ImageField.prototype.getValue = function()
{
    return new ag.api.Object("admin.v1/Image", {
        data : ImageField.urlToObj(this.image.src).data,
        description : this.description
    });
};

ImageField.prototype.setValue = function(value)
{
    this.addClass("processing");
    this.origImage.src = ImageField.objToUrl(value);

    this.description = value.description;
};

// used for insertions via file select dialog
ImageField.prototype.setValueFromBlobUrl = function(blobUrl)
{
    this.addClass("processing");
    this.origImage.src = blobUrl;
    this.description = "";
};

// static
ImageField.objToUrl = function(imgObj)
{
    return "data:" +  imgObj.type + ";base64," + imgObj.data;
};

// static
ImageField.urlToObj = function(dataUrl)
{
    var rawParts = dataUrl.split("base64,");

    return new ag.api.Object("admin.v1/Image", {
        type : rawParts[0].replace(/^data:/, ""),
        data : rawParts[1]
    });
};

ag.admin.field.Image = ImageField;

})();
