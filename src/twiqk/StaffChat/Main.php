<?php

declare(strict_types=1);

namespace twiqk\StaffChat;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener

{

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->notice("Enabled. By twiqk!");
        @mkdir($this->getDataFolder());
        $this->saveDefaultConfig();
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        switch ($command->getName())
        {
            case "sc":
                if ($sender instanceof Player)
                {
                    $perms = $this->getConfig()->get("perms");
                    if ($sender->hasPermission($perms) || $sender->isOp())
                    {
                        if (!(isset($args[0])))
                        {
                            if (isset($this->enabled[strtolower($sender->getDisplayName())]))
                            {
                                unset($this->enabled[strtolower($sender->getDisplayName())]);
                            }
                            else
                            {
                                $this->enabled[strtolower($sender->getDisplayName())] = strtolower($sender->getDisplayName());
                            }

                            if (isset($this->enabled[strtolower($sender->getDisplayName())]))
                            {
                                $scj = $this->getConfig()->get("entersc");
                                $sender->sendMessage(TextFormat::GRAY . $scj );
                            }
                            else
                            {
                                $scq = $this->getConfig()->get("leavesc");
                                $sender->sendMessage(TextFormat::GRAY . $scq );
                            }
                            return true;
                        }
                    }
                }
                else
                {
                    $this->getLogger()->info("Please run this ingame");
                }
        }
    }

    public function onChat(PlayerChatEvent $event)
    {
        $sender = $event->getPlayer();
        if ($event->isCancelled())
        {
            return;
        }
        if (!(isset($this->enabled[strtolower($event->getPlayer()->getDisplayName())])))
        {
            return true;
        }

        $msg = $event->getMessage();
        if (!(isset($msg)))
        {
            return;
        }

        foreach ($this->getServer()->getOnlinePlayers() as $onlinePlayer)
        {
            if(isset($this->enabled[strtolower($onlinePlayer->getDisplayName())]))
            {
                $onlinePlayer->sendMessage(TextFormat::GRAY . "[SC] "  .TextFormat::GOLD . $sender->getDisplayName() . TextFormat::GRAY . " > " . TextFormat::GOLD . $msg);
            }
            $event->setCancelled(true);
        }

    }

}
