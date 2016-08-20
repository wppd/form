<?php

namespace Wppd\Form\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Console\AppNamespaceDetectorTrait;

class Form extends Command
{
    use AppNamespaceDetectorTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'wppd:form {name : the file name of the view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate form view based on user input';

    protected $file;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $file)
    {
        $this->file = $file;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $fields = [];

        $file = $this->argument("name");

        $model = $this->ask("Model Variable");

        $route = $this->ask("The form action route alias");

        while (1) {

            $field = $this->ask('The field_label:field_name:field_type (leave blank if done)', false);

            if (!$field) break;

            list($label, $name, $type) = explode(":", $field);

            $fields[] = compact('label', 'name', 'type');

        }

        $this->generateView($file, $model, $route, $fields);

    }

    protected function generateView($file, $model, $route, $fields)
    {
        $stubs_path = __DIR__.'/../stubs/';

        $form = $this->file->get($stubs_path . 'form.stub');

        $contents = "\n";

        foreach ($fields as $field) {

            $path = $stubs_path . strtolower($field['type']) . '.stub';

            if (!$this->file->exists($path))
                continue;

            $content = $this->file->get($path);

            $contents = $contents . str_replace(
                                        ['{label}', '{name}'],
                                        [$field['label'], $field['name']],
                                        $content
                                    ). "\n";
        }

        $form = str_replace(
                    ['{model}', '{route}', '{fields}'],
                    [$model, $route, $contents],
                    $form
                );

        $path = resource_path("views/".$file.'.blade.php');

        $this->file->put($path, $form);

        $this->info("file has been saved to {$path}");



    }
}
