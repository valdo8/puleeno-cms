<?php

namespace Platovies;

class Bootstrap {
    protected function init() {
    }

    protected function loadComposer() {
    }

    protected function setup() {
    }

    public function boot() {
        $this->init();
        $this->loadComposer();
        $this->setup();
        $this->run();
    }

    protected function run() {
    }
}
