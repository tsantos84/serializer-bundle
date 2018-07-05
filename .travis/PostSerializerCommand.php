<?php
/**
 * This file is part of the TSantos Serializer Bundle package.
 *
 * (c) Tales Santos <tales.augusto.santos@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\Repository\PostRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TSantos\Serializer\SerializerAwareInterface;
use TSantos\Serializer\Traits\SerializerAwareTrait;

/**
 * Class SerializerCommand
 *
 * @author Tales Santos <tales.augusto.santos@gmail.com>
 */
class PostSerializerCommand extends Command implements SerializerAwareInterface
{
    use SerializerAwareTrait;

    /**
     * @var PostRepository
     */
    private $posts;

    /**
     * PostSerializerCommand constructor.
     * @param PostRepository $posts
     */
    public function __construct(PostRepository $posts)
    {
        parent::__construct();
        $this->posts = $posts;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Serialize posts for test purpose')
            ->setName('serializer:posts');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->serializer->serialize($this->posts->findLatest()));
    }
}
