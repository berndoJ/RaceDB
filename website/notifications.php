<div>
    <style>
        .notification-panel {
            z-index: 10;
            position: fixed;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }

        .notification-panel ul {
            margin: 0;
            padding: 0;
            float: right;
            width: 35%;
        }

        .notification-panel ul div {
            margin: 10px;
            pointer-events: auto;
        }

        .notification-body {
            padding: 20px;
            position: relative;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-animation-name: animateright;
            -webkit-animation-duration: 0.4s;
            animation-name: animateright;
            animation-duration: 0.4s;
        }

        @-webkit-keyframes animateright {
            from {
                right: -500px;
                opacity: 0
            }

            to {
                right: 0;
                opacity: 1
            }
        }

        @keyframes animateright {
            from {
                right: -500px;
                opacity: 0
            }

            to {
                right: 0;
                opacity: 1
            }
        }

        .notification-body[type="error"] {
            background-color: #f44336;
            color: #ffffff;
        }

        .notification-body[type="info"] {
            background-color: #0099ff;
            color: #ffffff;
        }

        .notification-body[type="success"] {
            background-color: #00cc00;
            color: #ffffff;
        }

        .notification-closebutton {
            margin-left: 15px;
            color: #ffffff;
            font-weight: bold;
            float: right;
            line-height: 20px;
            font-size: 22px;
            cursor: pointer;
            transition: 0.3s;
        }

        .notification-closebutton:hover {
            color: #000000;
        }
    </style>

    <div class="notification-panel">
        <ul></ul>
    </div>
</div>

<script type="application/javascript">
    var notification_template = `
    <div class="notification-body" type="{type}" id="NOTIFICATION-{rnd}">
        <span class="notification-closebutton" onclick="$(this).parent().remove();">&times;</span>
        {text}
        <script type="application/javascript">
        setTimeout(function(){
            $("#NOTIFICATION-{rnd}").remove();
        }, 7500);
        <\/script>
    </div>`;

    function display_notification(type, html) {
        $(".notification-panel ul").append(notification_template.replace("{type}", type).replace("{text}", html).replace(/\{rnd\}/g, Math.floor(Math.random() * 10000000)));
    }
</script>