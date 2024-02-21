var toastCtrl = {
    toast : false,
    body : false,
    createUUID : function()
    {
        var d = new Date().getTime();

        if (window.performance && typeof window.performance.now === "function") {
            d += performance.now(); 
        }

        var uuid = 'Zxxx-Axxx'.replace(/[xy]/g, function (c) {
            var r = (d + Math.random() * 16) % 16 | 0;
            d = Math.floor(d / 16);
            return (c == 'x' ? r : (r & 0x3 | 0x8)).toString(16);
        });

        return uuid;
    },
    showMessage : function(title,subTitle)  {
        this.toast({
            title : title,
            subTitle : subTitle,
        });
    },
    create : function(options)  {
        var defaults = {
            bgClass: "bg-primary",
            message: "",
            id: this.createUUID(),
            open: function () { }, 
        };

        this.settings = {...defaults, ...options};
        
        var $toastContainer = $("<div />").addClass("toast-container position-fixed bottom-0 end-0 p-3 sans")
        var $toast = $("<div />").attr("id", 'liveToast').attr("role", "alert").attr("aria-live", "assertive").attr("aria-atomic", "true").addClass(`toast align-items-center text-white border-0 ${this.settings.bgClass}`)
        var $toastFlex = $("<div />").addClass("d-flex")
        
        $toast.shown = false;
        
        var $button = $("<button />").addClass("btn-close btn-close-white me-2 m-auto").attr("type","button").attr("data-bs-dismiss","toast").attr("aria-label","Close")
        var $body = $("<div />").addClass("toast-body").html(this.settings.message)

        $toastFlex.append($body,$button)

        $toast.append($toastFlex)

        $toastContainer.append($toast)

        this.settings.open($toast);

        this.toast = $toast;
        this.body = $body;

        $toast.dismiss = function(callback)
        {
            $toast.toast('hide');
        }

        $toast.on('shown.bs.toast', function (e) {
            $toast.shown = true;
        });

        $("body").append($toastContainer)

        return this;
    },
    toast : function(settings)  {
        let toast = this.create(settings);
        this.present(toast.toast);
    },
    present : function($toast)  {
        console.log($toast.toast)

        $toast.toast.toast("show")

        // $toast.toast("show");
    },
};