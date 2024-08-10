<?php

namespace vendor\main\request\validating;

class Validation
{
  private $inputs, $ruleInput;

  public function __construct($inputs, $ruleInput)
  {
    $this->inputs = $inputs;
    $this->ruleInput = $ruleInput;
  }

  public const messages = [
    Rule::required => "Le champ `<!>` est requis",
    Rule::requiredFile => "Le fichier `<!>` est requis",
    Rule::text => "Le champ `<!>` doit être un texte",
    Rule::number => "Le champ `<!>` doit être un nombre",
    Rule::equal => "Le champ `<!>` doit être égal",
    Rule::superior => "Le champ `<!>` doit être supérieur",
    Rule::superiorOrEqual => "Le champ `<!>` doit être supérieur ou égal",
    Rule::inferior => "Le champ `<!>` doit être inférieur",
    Rule::inferiorOrEqual => "Le champ `<!>` doit être inférieur ou égal",
    Rule::in => "Le champ `<!>` doit être dans les choix possibles",
    -Rule::text => "Le champ `<!>` ne doit pas être un texte",
    -Rule::number => "Le champ `<!>` ne doit pas être un nombre",
    -Rule::equal => "Le champ `<!>` ne doit pas être égal",
    -Rule::superior => "Le champ `<!>` ne doit pas être supérieur",
    -Rule::superiorOrEqual => "Le champ `<!>` ne doit pas être supérieur ou égal",
    -Rule::inferior => "Le champ `<!>` ne doit pas être inférieur",
    -Rule::inferiorOrEqual => "Le champ `<!>` ne doit pas être inférieur ou égal",
    -Rule::in => "Le champ `<!>` ne doit pas être dans les choix possibles",
  ];

  public function validate()
  {
    $ruleInput = $this->ruleInput;
    $inputs = $this->inputs;
    foreach ($ruleInput->rules as $rule) {
      switch ($rule['type']) {
        case Rule::required:
          if ($this->required())
            continue 2;
          return [false, Rule::required];
        case Rule::optional:
          if (isset($inputs[$ruleInput->field]))
            continue 2;
          return true;
        case Rule::text:
          if ($this->text())
            continue 2;
          return [false, Rule::text];
        case Rule::number:
          if ($this->number())
            continue 2;
          return [false, Rule::number];
        case Rule::in:
          if ($this->in($rule['args']))
            continue 2;
          return [false, Rule::in];
        case Rule::equal:
          if ($this->equal($rule['args']))
            continue 2;
          return [false, Rule::equal];
        case Rule::superior:
          if ($this->superior($rule['args']))
            continue 2;
          return [false, Rule::superior];
        case Rule::superiorOrEqual:
          if ($this->superiorOrEqual($rule['args']))
            continue 2;
          return [false, Rule::superiorOrEqual];
        case Rule::inferior:
          if ($this->inferior($rule['args']))
            continue 2;
          return [false, Rule::inferior];
        case Rule::inferiorOrEqual:
          if ($this->inferiorOrEqual($rule['args']))
            continue 2;
          return [false, Rule::inferiorOrEqual];
        case Rule::not:
          if (($ret = $this->not($rule['args'])) === true)
            continue 2;
          return $ret;
        case Rule::requiredFile:
          if ($this->requiredFile())
            continue 2;
          return [false, Rule::requiredFile];
      }
    }
    return true;
  }

  private function not($args)
  {
    $ruleInput = $this->ruleInput;
    $inputs = $this->inputs;
    foreach ($args as $rule)
      switch ($rule) {
        case Rule::text:
          if (!$this->text())
            continue 2;
          return [false, -Rule::text];
        case Rule::number:
          if (!$this->number())
            continue 2;
          return [false, -Rule::number];
        case Rule::in:
          if (!$this->in($rule['args']))
            continue 2;
          return [false, -Rule::in];
        case Rule::equal:
          if (!$this->equal($rule['args']))
            continue 2;
          return [false, -Rule::equal];
        case Rule::superior:
          if (!$this->superior($rule['args']))
            continue 2;
          return [false, -Rule::superior];
        case Rule::superiorOrEqual:
          if (!$this->superiorOrEqual($rule['args']))
            continue 2;
          return [false, -Rule::superiorOrEqual];
        case Rule::inferior:
          if (!$this->inferior($rule['args']))
            continue 2;
          return [false, -Rule::inferior];
        case Rule::inferiorOrEqual:
          if (!$this->inferiorOrEqual($rule['args']))
            continue 2;
          return [false, -Rule::inferiorOrEqual];
      }
    return true;
  }

  private function number()
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    return is_numeric($inputs[$rule->field]);
  }

  private function requiredFile()
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    return !empty($inputs[$rule->field]['name']) && $inputs[$rule->field]['name'] !== '';
  }

  private function required()
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    return isset($inputs[$rule->field]) && $inputs[$rule->field] != "" && $inputs[$rule->field] != null;
  }

  private function text()
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    return !is_numeric($inputs[$rule->field]);
  }

  private function in($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    foreach ($args as $el) {
      if (is_array($el) && isset($el['valueof'])) {
        if ($inputs[$el['valueof']] == $inputs[$rule->field]) return true;
      } else {
        if ($el == $inputs[$rule->field]) return true;
      }
    }
    return false;
  }

  private function equal($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    if (is_array($args) && isset($args['valueof'])) {
      return $inputs[$rule->field] == $inputs[$args['valueof']];
    }
    return $inputs[$rule->field] == $args;
  }

  private function superior($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    if (is_array($args) && isset($args['valueof'])) {
      return $inputs[$rule->field] > $inputs[$args['valueof']];
    }
    return $inputs[$rule->field] > $args;
  }

  private function superiorOrEqual($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    if (is_array($args) && isset($args['valueof'])) {
      return $inputs[$rule->field] >= $inputs[$args['valueof']];
    }
    return $inputs[$rule->field] >= $args;
  }

  private function inferior($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    if (is_array($args) && isset($args['valueof'])) {
      return $inputs[$rule->field] < $inputs[$args['valueof']];
    }
    return $inputs[$rule->field] < $args;
  }

  private function inferiorOrEqual($args)
  {
    $inputs = $this->inputs;
    $rule = $this->ruleInput;
    if (is_array($args) && isset($args['valueof'])) {
      return $inputs[$rule->field] <= $inputs[$args['valueof']];
    }
    return $inputs[$rule->field] <= $args;
  }
}
