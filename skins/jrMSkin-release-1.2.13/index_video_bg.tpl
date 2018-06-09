
{assign var=unique_id value=1|mt_rand:1}
{if $_conf.jrMSkin_randomize == 'off'}
    {assign var=unique_id value=$_conf.jrMSkin_default_video}
{/if}

<div class="video_wrapper">
    <video autoplay="true" loop="true" muted="true" id="background">
        <source src="{$jamroom_url}/skins/jrMSkin/video_bgs/{$unique_id}.mp4?_v={$smarty.now}" type="video/mp4">
        <source src="{$jamroom_url}/skins/jrMSkin/video_bgs/{$unique_id}.webm?_v={$smarty.now}" type="video/webm">
    </video>
</div>