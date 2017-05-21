<section class="index">
    <div class="video_wrapper">
        <video autoplay="true" loop="true" muted="true" poster="{$jamroom_url}/skins/jrCelebrity/video_bgs/placeholder_1.jpg" id="background">
            <source src="{$jamroom_url}/skins/jrCelebrity/video_bgs/1.mp4?_v={$smarty.now}" type="video/mp4">
            <source src="{$jamroom_url}/skins/jrCelebrity/video_bgs/1.webm?_v={$smarty.now}" type="video/webm">
        </video>
    </div>
    <div class="overlay">
        <div class="middle">
            <div class="row">
                {jrCore_image image="logo_large.png" width="650" class="logo_large"}
                {jrCore_module_url module="jrUser" assign="uurl"}
                <div class="buttons">
                    <button class="login" onclick="jrCore_window_location('/{$uurl}/login')">Log in</button>
                    <button class="signup" onclick="jrCore_window_location('/{$uurl}/signup')">Register</button>
                </div>
            </div>
        </div>
    </div>
    <div class="down">
        <a href="#"></a>
    </div>
</section>