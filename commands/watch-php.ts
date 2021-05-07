import {CommandModule} from 'yargs';
import * as chokidar from 'chokidar';
import * as execa from 'execa';
import log from '@gitsync/log';
import theme from 'chalk-theme';
import {ExecaReturnValue} from 'execa';
import * as path from 'path';

const command: Partial<CommandModule> = {};

command.command = path.basename(__filename, '.ts');

command.describe = 'Watching plugin directories and update cache when file changed';

command.handler = async () => {
  await watchPluginConfig();
  await watchEvents();
  await watchGAutoCompletion();
};

async function watchPluginConfig() {
  log.info('Start scanning files.');

  let ready = false;

  chokidar.watch([
    'plugins/*/src/*Plugin.php',
    'plugins/*/src/Service/*.php',
    'plugins/*/pages/**/*.php',
  ])
    .on('add', listener)
    .on('unlink', listener)
    .on('ready', () => {
      ready = true;
      log.info('Ready for change.');
    });

  async function listener(path: string) {
    if (!ready) {
      return;
    }

    log.info('Change', path);

    const args = [
      'artisan',
      'plugin:refresh',
    ];
    startLog(args);

    const result = await execa('php', args);
    endLog(args, result);
  }
}

async function watchGAutoCompletion(): Promise<void> {
  const watcher = chokidar.watch([
    'plugins/*/src/Service/*.php',
  ]);

  watcher.on('change', async (path) => {
    log.info('Change', path);

    const paths = path.split('/');

    const args = [
      'artisan',
      'g:auto-completion',
      paths[1],
    ];
    startLog(args);

    const result = await execa('php', args);
    endLog(args, result);
  });
}

async function watchEvents() {
  const watcher = chokidar.watch([
    'plugins/*/src/*Plugin.php',
  ]);

  watcher.on('change', async (path) => {
    log.info('Change', path);

    const args = [
      'artisan',
      'event:refresh',
    ];
    startLog(args);

    const result = await execa('php', args);
    endLog(args, result);
  });
}

let start: Date;

function startLog(args: string[]) {
  log.info(`run command: ${args.join(' ')}`);
  start = new Date();
}

function endLog(args: string[], result: ExecaReturnValue<string>) {
  log.trace(
    'command: %s, duration: %s, exit code: %s, output: %s',
    theme.info(args[0]),
    theme.info((new Date().getMilliseconds() - start.getMilliseconds()).toString() + 'ms'),
    theme.info(result.exitCode.toString()),
    result.all,
  );
}

export default command;
/* @internal */
export {watchGAutoCompletion};
