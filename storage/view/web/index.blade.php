<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>D721</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.4.1/jquery.min.js"
            integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.4/clipboard.min.js"></script>
    <script src="https://unpkg.com/sodajs@0.4.10/dist/soda.min.js"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
</head>

<body>
<div class="container">
    <section class="hero is-fullheight is-small">
        <div class="hero-head">
            <div class="container">
                <nav id="navbar" class="navbar" role="navigation" aria-label="main navigation">
                    <div class="navbar-brand">
                        <a class="navbar-item" href="/">
                            <img src="https://bulma.io/images/bulma-logo.png" width="112" height="28">
                        </a>

                        <a role="button" class="navbar-burger burger" data-target="navMenu" aria-label="menu" aria-expanded="false">
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                            <span aria-hidden="true"></span>
                        </a>
                    </div>

                    <div id="navbarBasicExample" class="navbar-menu"  id="navMenu">
                        <div class="navbar-start">
                            <a class="navbar-item">
                                Home
                            </a>

                            <a class="navbar-item">
                                Documentation
                            </a>

                            <div class="navbar-item has-dropdown is-hoverable">
                                <a class="navbar-link">
                                    More
                                </a>

                                <div class="navbar-dropdown">
                                    <a class="navbar-item">
                                        About
                                    </a>
                                    <a class="navbar-item">
                                        Jobs
                                    </a>
                                    <a class="navbar-item">
                                        Contact
                                    </a>
                                    <hr class="navbar-divider">
                                    <a class="navbar-item">
                                        Report an issue
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="navbar-end">
                            <div class="navbar-item">
                                <div class="buttons">
                                    <a class="button is-primary">
                                        <strong>Sign up</strong>
                                    </a>
                                    <a class="button is-light">
                                        Log in
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
        <div class="hero-body">
            <div class="container">
                <nav class="level">
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">转换量</p>

                            <p class="title">{{ $id_count ?? '--'}}</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">访问量</p>

                            <p class="title">{{ $access_num ?? '--' }}</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">IP数</p>

                            <p class="title">{{ $ip_count ?? '--' }}</p>
                        </div>
                    </div>
                    <div class="level-item has-text-centered">
                        <div>
                            <p class="heading">今日活跃</p>

                            <p class="title">{{ $ip_day_count ?? '--' }}</p>
                        </div>
                    </div>
                </nav>

                <div class="columns">
                    <div class="column is-11">
                        <div class="field">
                            <div class="control level-left">
                                <input name="uri" class="input is-primary is-rounded" type="text" autocomplete="off"
                                       placeholder="输入源链接地址 (http://baidu.com)">
                            </div>
                        </div>
                    </div>
                    <div class="column">
                        <button class="button is-primary is-outlined is-rounded" id="go">生成</button>
                    </div>
                </div>
                <hr>
                <div id="message">

                </div>
                <hr>
                <div id="resp">

                </div>
            </div>
        </div>
        <div class="hero-foot">
            <!-- BUTTOM -->
            <footer class="footer has-background-white">
                <div class="content has-text-dark">
                    <p>
                        <strong>Power</strong> By <a href="https://alonexy.com">alonexy</a>
                    </p>
                </div>
            </footer>
        </div>
    </section>
</div>

</body>

</html>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {

        // Get all "navbar-burger" elements
        const $navbarBurgers = Array.prototype.slice.call(document.querySelectorAll('.navbar-burger'), 0);

        // Check if there are any navbar burgers
        if ($navbarBurgers.length > 0) {

            // Add a click event on each of them
            $navbarBurgers.forEach( el => {
                el.addEventListener('click', () => {

                    // Get the target from the "data-target" attribute
                    const target = el.dataset.target;
                    const $target = document.getElementById(target);

                    // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
                    el.classList.toggle('is-active');
                    $target.classList.toggle('is-active');

                });
            });
        }

    });
    $(".navbar-burger").click(function() {

        // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
        $(".navbar-burger").toggleClass("is-active");
        $(".navbar-menu").toggleClass("is-active");

    });
</script>
<script type="text/javascript">
    var resp_tpl = `<div>
		<h6 class="content animated pulse">
			短链地址:<a href="@{{dwz}}" target="_blank" class="is-link"> @{{dwz}} </a>
			<a href="javascript:void(0);" id="copy" class="button is-info is-outlined is-rounded is-small" data-clipboard-text="@{{dwz}}">copy</a>
			<span class="icon has-text-success">
				<i class="fas fas fa-check-circle"></i>
			</span>
		</h6>	
</div>`;
    var messageTpl = `<article class="message animated shake  is-@{{type}} is-small">
  <div class="message-header">
    <p>@{{title}}</p>
  </div>
  <div class="message-body">
    @{{content}}
  </div>
</article>`;


    var msgData = {
        type: "info",
        title: "提示",
        content: "所有转换的链接，有效期为一年."
    };
    $("#message").html(soda(messageTpl, msgData));

    var clipboard = new ClipboardJS('#copy');
    clipboard.on('success', function (e) {
        toastr.info('Copy Suc.');
        e.clearSelection();
    });

    clipboard.on('error', function (e) {
        console.error('Action:', e.action);
        console.error('Trigger:', e.trigger);
        toastr.warning('Copy Fail.');
    });
    $("#go").bind({
        click: function () {
            $(this).addClass("is-loading");
            var that = $(this);
            var uri = $("input[name='uri']").val();
            $.ajax({
                type: "POST",
                url: "/d", //路径
                data: {
                    'uri': uri
                },
                dataType: "json",
                success: function (result) { //返回数据根据结果进行相应的处理
                    that.removeClass("is-loading");
                    if (result.status > 0) {
                        var errMsgData = {
                            type: "danger",
                            title: "请求失败",
                            content: result.msg
                        };
                        $("#message").html(soda(messageTpl, errMsgData));
                        return false;
                    }
                    msgData.type = "success";
                    msgData.title = "请求成功";
                    $("#message").html(soda(messageTpl, msgData));
                    var rdata = {
                        dwz: result.data.juri
                    };
                    $("#resp").html(soda(resp_tpl, rdata));
                    return true;
                },
                error: function (error) {
                    var errMsgData = {
                        type: "danger",
                        title: "请求失败",
                        content: error.statusText
                    };
                    $("#message").html(soda(messageTpl, errMsgData));
                }
            });

        }
    });
</script>