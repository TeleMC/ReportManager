<?PHP

namespace ReportManager;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use UiLibrary\UiLibrary;

class ReportManager extends PluginBase {

    private static $instance = null;
    public $pre = "§e•";

    public static function getInstance() {
        return self::$instance;
    }

    public function onLoad() {
        self::$instance = $this;
    }

    public function onEnable() {
        date_default_timezone_set("Asia/Seoul");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
        @mkdir($this->getDataPath());
        @mkdir($this->getDataPath() . $this->getToday());
        foreach ($this->list() as $key => $value) {
            @mkdir($this->getDataPath() . $this->getToday() . "/" . $value);
        }
        $this->ui = UiLibrary::getInstance();
    }

    public function getDataPath() {
        //return "/root/Server/Data/ReportManager/";
        return "C:/Users/mial7/Downloads/root/Server/Data/ReportManager/";
    }

    public function getToday() {
        return date("Y-m-d", time());
    }

    public function list() {
        return [
                "불법프로그램 사용",
                "부적절한 언어 사용",
                "게임 진행 방해",
                "시스템",
                "기타"
        ];
    }

    public function ReportUI(Player $player) {
        $form = $this->ui->CustomForm(function (Player $player, array $data) {
            if (!isset($data[1]) || !isset($data[2]) || !isset($data[3])) {
                $player->sendMessage("{$this->pre} 제출에 실패하였습니다.");
                return false;
            }
            if ($data[2] !== 3 && $this->getServer()->getPlayer($data[1]) instanceof Player)
                $target = $this->getServer()->getPlayer($data[1])->getName();
            else
                $target = $data[1];

            if ($data[2] !== 3 && !file_exists($this->getServer()->getDataPath() . "players/" . mb_strtolower($target) . ".dat")) {
                $player->sendMessage("{$this->pre} 해당 유저는 존재하지 않습니다.");
                return false;
            }

            if ($this->addReport($player->getName(), $data[1], $data[2], $data[3])) {
                $player->sendMessage("{$this->pre} 신고가 접수되었습니다.");
                return true;
            } else {
                $player->sendMessage("{$this->pre} 10회가 초과되었습니다.");
                return false;
            }
        });
        $form->setTitle("Tele Report");
        $form->addLabel("§c▶ §f온라인 유저를 신고합니다.\n§c▶ §f인게임에서 신고시, 증거물은 첨부하실 수 없으며,\n  서버 로그에 의존하여 처리됩니다.\n§c▶ §f같은 유저를 대상으로 최대 10번 신고 가능하며,\n  장난식으로 작성시 제재 받을 수 있습니다.\n§c▶ §f시스템 오류 신고시, 신고대상은 오류사항으로,\n  신고사항은 시스템으로 지정해주세요.");
        $form->addInput("§l§c▶ §r§f신고 대상", "NickName", "");
        $form->addDropdown("§l§6▶ §r§f신고 사유", $this->list());
        $form->addInput("§l§a▶ §r§f자세한 신고 사항", "Message", "");
        $form->sendToPlayer($player);
    }

    public function addReport(string $name, string $target, int $cause, string $message) {
        $name = mb_strtolower($name);
        $target = mb_strtolower($target);
        $cause = ($this->list())[$cause];
        $title = $name . " - " . $target;
        if ($this->getServer()->getPort() == 19131)
            $server = "제네시스";
        elseif ($this->getServer()->getPort() == 19130)
            $server = "브레아";

        $value = "";
        $value .= "신고자: {$name}\n";
        $value .= "신고대상: {$target}\n";
        $value .= "신고사유: {$cause}\n";
        $value .= "기타사항: {$message}\n";
        $value .= "\n";
        $value .= "접수일: {$this->getToday()} {$this->getTime()}";

        for ($i = 0; $i < 10; $i++) {
            if (!file_exists($this->getDataPath() . $this->getToday() . "/" . $cause . "/" . $title . " | " . $server . " | " . $i . ".txt")) {
                file_put_contents($this->getDataPath() . $this->getToday() . "/" . $cause . "/" . $title . " | " . $server . " | " . $i . ".txt", $value);
                return true;
            }
        }
        return false;
    }

    public function getTime() {
        return date("h시 i분", time());
    }
}
