{include file="$tplfooter.tpl"}
<div style="display: none;" class="pop-outer text-right">
        <div class="pop-inner">
            <button class="close-popup btn btn-danger">X</button>
            <img src="" width="100%">
        </div>
    </div>
    <script>
        $(document).ready(function (){
            $('body').on('click', '.img-popup', function (){
                $('.pop-outer img').attr("src", $(this).attr("data-img"));
                $(".pop-outer").fadeIn("slow");       
            });
            $(".close-popup").click(function (){
                $(".pop-outer").fadeOut("slow");
            });
        });
    </script>
    <style>
        .pop-outer {
            background-color: rgba(0, 0, 0, 0.5);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
        .pop-inner {
            background-color: #fff;
            width: 500px;
            height: auto;
            padding: 10px;
            margin: 5% auto;
        }

        img.img-popup {
            cursor: pointer;
        }        
    </style>    