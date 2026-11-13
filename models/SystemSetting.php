<?php
require_once __DIR__ . "/../lib/BaseModel.php";
class SystemSetting extends BaseModel {
    protected $table = "system_settings";
    public function get($key, $default = null) {
        $row = $this->findOne(["setting_key" => $key]);
        return $row ? $row["setting_value"] : $default;
    }
    public function set($key, $value) {
        $existing = $this->findOne(["setting_key" => $key]);
        if ($existing) return $this->update($existing["id"], ["setting_value" => $value]);
        return $this->insert(["setting_key" => $key, "setting_value" => $value]);
    }
    public function getAll() {
        $rows = $this->findAll(); $result = [];
        foreach ($rows as $row) $result[$row["setting_key"]] = $row["setting_value"];
        return $result;
    }
}