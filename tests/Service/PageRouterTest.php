<?php

namespace MiaoxingTest\Plugin\Service;

use Miaoxing\Plugin\Test\BaseTestCase;

/**
 * @mixin \PageRouterMixin
 */
class PageRouterTest extends BaseTestCase
{
    /**
     * @param string $pathInfo
     * @param array|null $result
     * @dataProvider providerForMatch
     */
    public function testMatch(string $pathInfo, array $result = null)
    {
        $this->pageRouter->setPages([
            'assignees' => [
                '_file' => 'index.php',
                '[assignee].php' => [],
            ],
            'issues' => [
                '_file' => 'index.php',
                'comments' => [
                    '_file' => 'index.php',
                    '[commentId].php' => [],
                ],

                '[issueNumber].php' => [],

                '[issueNumber]' => [
                    'assignees' => [
                        '_file' => 'index.php',
                    ],

                    'comments' => [
                        '_file' => 'index.php',
                        '[commentId].php' => [],
                    ],

                    'lock' => [
                        '_file' => 'index.php',
                    ],
                ],
            ],
        ]);

        if ($result && !isset($result['params'])) {
            $result['params'] = [];
        }

        $this->assertSame($result, $this->pageRouter->match($pathInfo));
    }

    public function providerForMatch()
    {
        return [
            [
                '/assignees',
                [
                    'file' => 'assignees/index.php',
                ],
            ],
            [
                '/assignees/test',
                [
                    'file' => 'assignees/[assignee].php',
                    'params' => [
                        'assignee' => 'test',
                    ],
                ],
            ],
            [
                '/assignees/0',
                [
                    'file' => 'assignees/[assignee].php',
                    'params' => [
                        'assignee' => '0',
                    ],
                ],
            ],
            [
                '/assignees/1/2',
                null,
            ],
            [
                '/issues',
                [
                    'file' => 'issues/index.php',
                ],
            ],
            [
                '/issues/2',
                [
                    'file' => 'issues/[issueNumber].php',
                    'params' => [
                        'issueNumber' => '2',
                    ],
                ],
            ],
            [
                '/issue',
                null,
            ],
            [
                '/issues/comments',
                [
                    'file' => 'issues/comments/index.php',
                ],
            ],
            [
                '/issues/not-comments',
                [
                    'file' => 'issues/[issueNumber].php',
                    'params' => [
                        'issueNumber' => 'not-comments',
                    ],
                ],
            ],
            [
                '/issues/comments/2',
                [
                    'file' => 'issues/comments/[commentId].php',
                    'params' => [
                        'commentId' => '2',
                    ],
                ],
            ],
            [
                '/issues/comments/assignees',
                [
                    'file' => 'issues/comments/[commentId].php',
                    'params' => [
                        'commentId' => 'assignees',
                    ],
                ],
            ],
            [
                '/issues/comments/2/3',
                null,
            ],
            [
                '/issues/3/assignees',
                [
                    'file' => 'issues/[issueNumber]/assignees/index.php',
                    'params' => [
                        'issueNumber' => '3',
                    ],
                ],
            ],
            [
                '/issues/3/comments',
                [
                    'file' => 'issues/[issueNumber]/comments/index.php',
                    'params' => [
                        'issueNumber' => '3',
                    ],
                ],
            ],
            [
                '/issues/3/comments/4',
                [
                    'file' => 'issues/[issueNumber]/comments/[commentId].php',
                    'params' => [
                        'issueNumber' => '3',
                        'commentId' => '4',
                    ],
                ],
            ],

            [
                '/issues/3/lock',
                [
                    'file' => 'issues/[issueNumber]/lock/index.php',
                    'params' => [
                        'issueNumber' => '3',
                    ],
                ],
            ],
        ];
    }

    public function testMatchExact()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
            ],
        ]);

        $this->assertSame('issues/index.php', $this->pageRouter->match('/issues')['file']);
    }

    public function testDir()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
                '_dir' => 'pages',
            ],
        ]);

        $this->assertSame('pages/issues/index.php', $this->pageRouter->match('/issues')['file']);
    }

    public function testMatchDynamic()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
                '[issueNumber].php' => [],
            ],
        ]);

        $this->assertSame([
            'file' => 'issues/[issueNumber].php',
            'params' => [
                'issueNumber' => '1',
            ],
        ], $this->pageRouter->match('/issues/1'));
    }

    public function testMatchNested()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '[issueNumber]' => [
                    'comments' => [
                        '_file' => 'index.php',
                        '[commentId].php' => [],
                    ],
                ],
                '[issueNumber].php' => [],
                '_file' => 'index.php',
            ],
        ]);

        $this->assertSame([
            'file' => 'issues/[issueNumber].php',
            'params' => [
                'issueNumber' => '1',
            ],
        ], $this->pageRouter->match('/issues/1'));

        $this->assertSame([
            'file' => 'issues/[issueNumber]/comments/index.php',
            'params' => [
                'issueNumber' => '1',
            ],
        ], $this->pageRouter->match('/issues/1/comments'));

        $this->assertSame([
            'file' => 'issues/[issueNumber]/comments/[commentId].php',
            'params' => [
                'issueNumber' => '1',
                'commentId' => '2',
            ],
        ], $this->pageRouter->match('/issues/1/comments/2'));
    }

    public function testMatchSimilar()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '[issueId]' => [
                    'labels' => [
                        '_file' => 'index.php',
                        '[labelId].php' => [],
                    ],
                ],
                '[issueNumber]' => [
                    'comments' => [
                        '_file' => 'index.php',
                        '[commentId].php' => [],
                    ],
                ],
                '[issueNumber].php' => [],
                '_file' => 'index.php',
            ],
        ]);

        $this->assertSame([
            'file' => 'issues/[issueNumber].php',
            'params' => [
                'issueNumber' => '1',
            ],
        ], $this->pageRouter->match('/issues/1'));

        $this->assertSame([
            'file' => 'issues/[issueId]/labels/index.php',
            'params' => [
                'issueId' => '1',
            ],
        ], $this->pageRouter->match('/issues/1/labels'));

        $this->assertSame([
            'file' => 'issues/[issueId]/labels/[labelId].php',
            'params' => [
                'issueId' => '1',
                'labelId' => '2',
            ],
        ], $this->pageRouter->match('/issues/1/labels/2'));
    }

    public function testMatchDeep()
    {
        $this->pageRouter->setPages([
            'orgs' => [
                '_file' => 'index.php',
                '[org].php' => [],
                '[org]' => [
                    'teams' => [
                        '[teamSlug]' => [
                            'discussions' => [
                                '[discussionNumber]' => [
                                    'comments' => [
                                        '[commentNumber]' => [
                                            'reactions' => [
                                                '_file' => 'index.php',
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertNull($this->pageRouter->match('/orgs/miaoxing/teams/miaoxing'));

        $this->assertSame([
            'file' => <<<F
orgs/[org]/teams/[teamSlug]/discussions/[discussionNumber]/comments/[commentNumber]/reactions/index.php
F,
            'params' => [
                'org' => 'miaoxing',
                'teamSlug' => 'miaoxing',
                'discussionNumber' => '1',
                'commentNumber' => '2',
            ],
        ], $this->pageRouter->match('/orgs/miaoxing/teams/miaoxing/discussions/1/comments/2/reactions'));
    }

    public function testIgnoreConfig()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
                '_dir' => 'apis',
            ],
        ]);

        $this->assertNull($this->pageRouter->match('/issues/_dir'));
        $this->assertSame('apis/issues/index.php', $this->pageRouter->match('/issues')['file']);
    }

    public function testIndexPhp()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
            ],
        ]);

        $this->assertNull($this->pageRouter->match('/issues/index.php'));
        $this->assertNull($this->pageRouter->match('/issues/index'));
        $this->assertSame('issues/index.php', $this->pageRouter->match('/issues')['file']);
    }

    public function testPreferNamePhpThanIndexPhp()
    {
        $this->pageRouter->setPages([
            'issues.php' => [],
            'issues' => [
                '_dir' => '',
                '_file' => 'index.php',
            ],
        ]);

        $this->assertSame('issues.php', $this->pageRouter->match('issues')['file']);
    }

    public function testOrder()
    {
        $this->pageRouter->setPages([
            'comments' => [
                '_file' => 'index.php',
            ],
            '[issueNumber].php' => [],
        ]);

        $this->assertSame('comments/index.php', $this->pageRouter->match('comments')['file']);

        $this->pageRouter->setPages([
            '[issueNumber].php' => [],
            'comments' => [
                '_file' => 'index.php',
            ],
        ]);

        $this->assertSame('[issueNumber].php', $this->pageRouter->match('comments')['file']);
    }

    public function testActionPage()
    {
        $this->pageRouter->setPages([
            'issues' => [
                '_file' => 'index.php',
                '[id].php' => [],
                '[id]' => [
                    'new.php' => [],
                    'edit.php' => [],
                ],
            ],
        ]);

        $this->assertSame([
            'file' => 'issues/[id]/edit.php',
            'params' => [
                'id' => '1',
            ],
        ], $this->pageRouter->match('issues/1/edit'));

        $this->assertSame([
            'file' => 'issues/[id].php',
            'params' => [
                'id' => '2',
            ],
        ], $this->pageRouter->match('issues/2'));
    }

    public function testDirContainsDotPhp()
    {
        $this->pageRouter->setPages([
            'issues.php' => [
                'labels.php' => [],
            ],
        ]);

        $this->assertSame('issues.php/labels.php', $this->pageRouter->match('issues.php/labels')['file']);
        $this->assertNull($this->pageRouter->match('issues/labels'));
        $this->assertNull($this->pageRouter->match('issues'));
        $this->assertNull($this->pageRouter->match('issues.php'));
    }

    public function testGeneratePage()
    {
        $dir = dirname(__DIR__) . '/Fixture/pages';
        $this->pageRouter->setPageDirGlob($dir . '/{tests,tests2}');
        $this->assertSame($dir . '/{tests,tests2}', $this->pageRouter->getPageDirGlob());

        $pages = $this->pageRouter->getPages();
        $this->assertSame([
            '_file' => 'index.php',
            '_dir' => $dir . '/tests',
            '[testId]' => [
                'comments' => [
                    '_file' => 'index.php',
                    '_dir' => $dir . '/tests',
                    '[id].php' => [
                        '_dir' => $dir . '/tests',
                    ],
                ],
            ],
            '[id].php' => [
                '_dir' => $dir . '/tests',
            ],
            '[id]' => [
                'new.php' => [
                    '_dir' => $dir . '/tests',
                ],
                'edit.php' => [
                    '_dir' => $dir . '/tests',
                ],
            ],
            'test.php' => [
                '_dir' => $dir . '/tests2',
            ],
        ], $pages);
    }
}
