#!/usr/bin/env node
/**
 * Rebuild the vendored MetaCentrum design files.
 * Usage: node build.mjs <scratch copy of metacentrum-design at the pinned revision>
 *   e.g. git -C ../metacentrum-design archive 30de28a | tar -x -C /tmp/mcds && node build.mjs /tmp/mcds
 * Runs the design's own lockfile install and build, copies its output unmodified, compiles components.scss
 * against it and records every hash in manifest.json. The design checkout itself is never built in.
 */
import { execFileSync } from 'node:child_process';
import { createHash } from 'node:crypto';
import { copyFileSync, existsSync, readFileSync, writeFileSync } from 'node:fs';
import { join, resolve } from 'node:path';

const here = import.meta.dirname;
const design = resolve(process.argv[2] ?? '');
if (!process.argv[2] || !existsSync(join(design, 'src/tokens/tokens.json'))) throw new Error('Usage: node build.mjs <scratch copy of metacentrum-design>');
if (existsSync(join(design, '.git'))) throw new Error('Refusing to build inside a checkout; export the revision to a scratch copy first.');

const run = (command, args) => execFileSync(command, args, { cwd: design, stdio: 'inherit' });
const sha256 = path => createHash('sha256').update(readFileSync(path)).digest('hex');
const version = name => JSON.parse(readFileSync(join(design, 'node_modules', name, 'package.json'), 'utf8')).version;

if (!existsSync(join(design, 'node_modules'))) run('npm', ['ci', '--no-audit', '--no-fund']);
run('npm', ['run', 'build']);
const copies = {
  'mc-elements.js': 'dist/mc-elements.js',
  'mc-elements.js.map': 'dist/mc-elements.js.map',
  'tokens.css': 'dist/tokens/variables.css',
  'foundation.css': 'dist/foundation.css',
};
for (const [file, from] of Object.entries(copies)) copyFileSync(join(design, from), join(here, file));
run('npx', ['sass', '--style=compressed', '--no-source-map', '--quiet-deps', '--load-path=node_modules', '--load-path=src/styles', '--load-path=.', join(here, 'components.scss'), join(here, 'components.css')]);

const manifestPath = join(here, 'manifest.json');
const manifest = JSON.parse(readFileSync(manifestPath, 'utf8'));
manifest.bundleSHA256 = sha256(join(here, 'mc-elements.js'));
manifest.files = Object.fromEntries([...Object.entries(copies), ['components.css', 'components.scss + dist/index.css (sass --style=compressed)']]
  .map(([file, from]) => [file, { from, sha256: sha256(join(here, file)) }]));
manifest.inputs = { 'components.scss': sha256(join(here, 'components.scss')), 'dist/index.css': sha256(join(design, 'dist/index.css')) };
for (const name of ['sass', '@carbon/styles', '@carbon/type', '@carbon/react', 'lit', 'tsup']) if (existsSync(join(design, 'node_modules', name))) manifest.dependencies[name] = version(name);
writeFileSync(manifestPath, JSON.stringify(manifest, null, 2) + '\n');
console.log(Object.entries(manifest.files).map(([file, entry]) => `${entry.sha256}  ${file}`).join('\n'));
