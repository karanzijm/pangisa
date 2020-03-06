<style>
    div::-webkit-scrollbar {
        width: 3px;
        background: #222;
    }

    div::-webkit-scrollbar-track {
        -webkit-box-shadow: none;
    }

    div::-webkit-scrollbar-thumb {
        background-color: #dc134c;
        height: 20px;
        outline: 1px solid slategrey;
    }
</style>

<div style="padding:0px 40px; max-height: 100%; background: #fff">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-3" style="height: inherit; padding: 10px; overflow-y: scroll;">
                <div style="padding-left: 10px;">
                    <h3>Recent Chats</h3>
                </div>
                <div>
                    <?php
                    $i = 0;
                    foreach ($data['chats'] as $chat) {
                        $backgroundColor = $i % 2 == 0 ? "#f5f5f5" : "fff";
                        $unread = $chat['unread'] > 0 ? "<small style='background: #dc134c; color: #fff; padding: 5px;'>" . $chat['unread'] . "</small>" : null;
                        echo "<a href='" . base_url() . "Messages/index/" . $chat['senderId'] . "'> <section style='background: " . $backgroundColor . "; padding:10px;'><b>" . $chat['sender'] . "</b>  &nbsp;&nbsp;&nbsp;" . $unread . "<br><small>" . $chat['message'] . "</small></section></a>";
                        $i++;
                    }
                    ?>
                    </h5>
                </div>
            </div>

            <div class="col-sm-9">
                <?php if ($data['chatWith'] <> null): ?>
                    <h4><?= ($data['chatWith'] <> null ? $data['chatWith']->name : null) ?></h4>
                    <div style="height: 80vh; overflow-y: scroll;">
                        <?php
                        foreach ($data['chatsMessages'] as $chat) {
                            $loggedInUser = $this->session->user['id'];
                            $textAlign = ($chat['senderId'] == $loggedInUser ? 'left' : 'left');
                            $textBackground = ($chat['senderId'] == $loggedInUser ? '#f5f5f5' : 'white');
                            $display_direction = ($chat['senderId'] == $loggedInUser ? 'right' : 'left');

                            echo "<p style='border-radius:10px; width:70%; margin:10px; position:relative; float:" . $display_direction . "; border:1px solid #ccc; padding:5px;  background:" . $textBackground . ";text-align: " . $textAlign . "'><small>" . $chat['date'] . "</small> &nbsp;&nbsp;&nbsp;" . $chat['message'] . "</p>";
                        }
                        ?>
                    </div>

                    <form style="border-top: 2px solid #dc134c;" method="post"
                          action="<?= base_url() ?>/Customer/Messages/<?= $data['chatWith']->id ?>">
                        <table cellspacing="0px" cellpadding="10px" width="100%">
                            <tr>
                                <td><input type="hidden" name="receiver" value="<?= $data['chatWith']->id ?>"/></td>
                            </tr>
                            <tr>
                                <td width="80%">
                                    <input type="text" name="message"
                                           style="width: 100%; border-radius: 40px; outline:none; border:2px solid #255; padding:10px 10px;"/>
                                </td>
                                <td>
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-mail-reply"></i> Send
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </form>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
