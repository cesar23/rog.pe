import os
import re
import shutil
from pathlib import Path
from datetime import datetime


# ================================================================
#  CONFIGURACIÓN — edita solo esta sección
# ================================================================

# Ruta raíz del proyecto
PROJECT_PATH = "."

# Carpeta de salida para los .md generados
OUTPUT_DIR = "docs_devops"

# ── Directorios que el script DEBE leer (lista blanca) ──────────
# Solo se documentan los ficheros que estén dentro de estas carpetas
# o en la raíz del proyecto.
# Añade o elimina según tu estructura.
INCLUDE_DIRS = {
    # Docker
    '.docker',
    '.devcontainer',
    # Git / plataformas
    '.github',
    '.gitlab',
    '.husky',
    # CI/CD
    '.circleci',
    '.azure-pipelines',
    '.teamcity',
    # Infraestructura como código
    'terraform',
    'ansible',
    'k8s',
    'kubernetes',
    'helm',
    'charts',
    # Despliegue / Ops genérico
    'infra',
    'deploy',
    'ops',
    'scripts',
    'envs',
    'environments',
    'pipeline',
    'pipelines',
    'ci',
    'cd',
    'cicd',
    '.jenkins',
}

# ── Ficheros en la RAÍZ del proyecto que siempre se incluyen ────
ROOT_FILES_INCLUDE = {
    # Docker
    'Dockerfile',
    'Dockerfile.dev',
    'Dockerfile.prod',
    'Dockerfile.staging',
    'docker-compose.yml',
    'docker-compose.yaml',
    'docker-compose.dev.yml',
    'docker-compose.dev.yaml',
    'docker-compose.prod.yml',
    'docker-compose.prod.yaml',
    'docker-compose.staging.yml',
    'docker-compose.staging.yaml',
    'docker-compose.override.yml',
    'docker-compose.override.yaml',
    '.dockerignore',
    # CI/CD raíz
    '.gitlab-ci.yml',
    '.gitlab-ci.yaml',
    'Jenkinsfile',
    'azure-pipelines.yml',
    'azure-pipelines.yaml',
    'bitbucket-pipelines.yml',
    '.travis.yml',
    'Makefile',
    # Terraform raíz
    'main.tf',
    'variables.tf',
    'outputs.tf',
    'providers.tf',
    'backend.tf',
    'terraform.tfvars',
    '.terraform-version',
    # Ansible raíz
    'ansible.cfg',
    'playbook.yml',
    'playbook.yaml',
    'requirements.yml',
    'inventory',
    'hosts',
    # Kubernetes raíz
    'skaffold.yaml',
    'skaffold.yml',
    'kustomization.yaml',
    'kustomization.yml',
    # Variables de entorno
    '.env',
    '.env.example',
    '.env.local',
    '.env.dev',
    '.env.development',
    '.env.staging',
    '.env.production',
    '.env.prod',
    '.env.test',
}

# ── Extensiones permitidas dentro de INCLUDE_DIRS ───────────────
# Solo se documentan ficheros con estas extensiones.
INCLUDE_EXTENSIONS = {
    # Config / IaC
    '.yml', '.yaml', '.json', '.toml', '.ini', '.cfg', '.conf', '.config',
    '.tf', '.tfvars', '.hcl',
    # Scripts
    '.sh', '.bash', '.zsh', '.ps1', '.bat', '.cmd', '.py', '.rb', '.pl',
    # Docker / Kubernetes
    '.dockerfile',
    # Nginx / Apache / proxy
    '.nginx', '.htaccess',
    # Otros texto plano relevantes
    '.env', '.properties', '.xml',
    # Sin extensión (Dockerfile, Makefile, Jenkinsfile, etc.) → manejados por nombre
}

# ── Nombres de fichero permitidos aunque no tengan extensión ─────
INCLUDE_NO_EXT_NAMES = {
    'Dockerfile', 'Makefile', 'Jenkinsfile',
    'Vagrantfile', 'Procfile',
    'inventory', 'hosts',
}

# ── Extensiones EXCLUIDAS (binarios, media, etc.) ────────────────
EXCLUDE_EXTENSIONS = {
    # Imágenes
    '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.webp',
    '.bmp', '.tiff', '.psd', '.ai', '.eps',
    # Fuentes
    '.woff', '.woff2', '.ttf', '.eot', '.otf',
    # Vídeo / Audio
    '.mp4', '.mp3', '.wav', '.avi', '.mov', '.ogg', '.flac', '.webm',
    # Comprimidos
    '.zip', '.rar', '.iso', '.tar', '.gz', '.7z', '.bz2', '.tgz',
    # Binarios / compilados
    '.exe', '.dll', '.so', '.bin', '.dat', '.class', '.jar',
    # Documentos ofimática
    '.pdf', '.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx',
    # Logs / datos raw
    '.log', '.csv',
    # Markdown (estructura, no contenido de infra)
    '.md', '.rst',
    # Bloqueos de dependencias
    '.lock',
}

# ── Directorios a ignorar completamente ─────────────────────────
EXCLUDE_DIRS = {
    'node_modules', 'vendor', '.git', '.idea', '.vscode',
    'dist', 'build', '__pycache__', '.cache', 'coverage',
    '.next', '.nuxt', 'out', '.turbo', 'tmp', 'temp',
    'target',   # Java/Maven
    'logs',
    'project_structure_docs',
    'Resources','www'
}

# ── Ficheros a ignorar por nombre ────────────────────────────────
EXCLUDE_FILES = {
    '.DS_Store', 'Thumbs.db', 'desktop.ini',
    'package-lock.json', 'yarn.lock', 'composer.lock',
    '.gitkeep', '.gitattributes',
}

# ── Valores sensibles: enmascarar en .env ────────────────────────
MASK_ENV_VALUES = True   # False para mostrar valores reales

# ================================================================
#  GENERADOR — no es necesario editar más abajo
# ================================================================

CATEGORY_ORDER = [
    'Docker',
    'Docker Compose',
    'Variables de Entorno',
    'GitHub Actions',
    'GitLab CI/CD',
    'Jenkins',
    'Azure DevOps',
    'CircleCI',
    'Otros CI/CD',
    'Terraform',
    'Ansible',
    'Kubernetes / Helm',
    'Scripts',
    'Nginx / Proxy',
    'Otros',
]

LANG_MAP = {
    '.yml':        'yaml',
    '.yaml':       'yaml',
    '.json':       'json',
    '.toml':       'toml',
    '.tf':         'hcl',
    '.tfvars':     'hcl',
    '.hcl':        'hcl',
    '.sh':         'bash',
    '.bash':       'bash',
    '.zsh':        'bash',
    '.ps1':        'powershell',
    '.bat':        'bat',
    '.cmd':        'bat',
    '.py':         'python',
    '.rb':         'ruby',
    '.xml':        'xml',
    '.properties': 'properties',
    '.ini':        'ini',
    '.cfg':        'ini',
    '.conf':       'nginx',
    '.nginx':      'nginx',
}


class DevOpsDocGenerator:

    def __init__(self, project_path=PROJECT_PATH, output_dir=OUTPUT_DIR):
        self.project_path = Path(project_path).resolve()
        self.output_dir   = Path(output_dir)
        self._clean_output()
        self.output_dir.mkdir(exist_ok=True)

    # ─── LIMPIEZA ────────────────────────────────────────────────

    def _clean_output(self):
        if self.output_dir.exists():
            print(f"🧹 Limpiando directorio anterior: {self.output_dir}")
            try:
                shutil.rmtree(self.output_dir)
                print("✅ Directorio limpiado")
            except Exception as e:
                print(f"⚠️  No se pudo limpiar: {e}")

    # ─── FILTROS ─────────────────────────────────────────────────

    def _is_excluded_dir(self, name: str) -> bool:
        return name in EXCLUDE_DIRS

    def _include_root_file(self, name: str) -> bool:
        """¿El fichero de la raíz debe incluirse?"""
        # Coincidencia exacta en la lista blanca
        if name in ROOT_FILES_INCLUDE:
            return True
        # Variantes Dockerfile.*
        if name.startswith('Dockerfile'):
            return True
        # Variantes docker-compose.*
        if name.startswith('docker-compose'):
            return True
        # Variantes .env.*
        if name.startswith('.env'):
            return True
        return False

    def _include_subdir_file(self, file_path: Path) -> bool:
        """¿El fichero dentro de un INCLUDE_DIR debe incluirse?"""
        name = file_path.name
        ext  = file_path.suffix.lower()

        if name in EXCLUDE_FILES:
            return False
        if ext in EXCLUDE_EXTENSIONS:
            return False

        # Sin extensión: solo si está en la lista blanca de nombres
        if ext == '':
            return (
                name in INCLUDE_NO_EXT_NAMES
                or name.startswith('Dockerfile')
                or name.startswith('.env')
            )

        return ext in INCLUDE_EXTENSIONS

    # ─── CATEGORIZACIÓN ──────────────────────────────────────────

    def _categorize(self, file_path: Path) -> str:
        name     = file_path.name
        path_str = str(file_path).lower().replace('\\', '/')

        # Docker
        if name.startswith('Dockerfile') or '.docker' in path_str or 'devcontainer' in path_str:
            return 'Docker'
        if name.startswith('docker-compose') or 'docker-compose' in path_str:
            return 'Docker Compose'

        # Variables de entorno
        if name.startswith('.env'):
            return 'Variables de Entorno'

        # CI/CD por plataforma
        if '.github' in path_str or 'github' in path_str:
            return 'GitHub Actions'
        if '.gitlab' in path_str or 'gitlab' in path_str:
            return 'GitLab CI/CD'
        if 'jenkins' in path_str or name == 'Jenkinsfile':
            return 'Jenkins'
        if 'azure' in path_str:
            return 'Azure DevOps'
        if 'circleci' in path_str:
            return 'CircleCI'
        if 'travis' in path_str or 'bitbucket' in path_str:
            return 'Otros CI/CD'

        # IaC
        if 'terraform' in path_str or file_path.suffix in {'.tf', '.tfvars', '.hcl'}:
            return 'Terraform'
        if 'ansible' in path_str or name in {'playbook.yml', 'playbook.yaml', 'ansible.cfg',
                                               'requirements.yml', 'inventory', 'hosts'}:
            return 'Ansible'
        if any(k in path_str for k in ('k8s', 'kubernetes', 'helm', 'charts',
                                        'kustomiz', 'skaffold')):
            return 'Kubernetes / Helm'

        # Scripts
        if any(k in path_str for k in ('scripts', 'script')) or \
           file_path.suffix in {'.sh', '.bash', '.zsh', '.ps1', '.bat', '.py'}:
            return 'Scripts'

        # Nginx / proxy
        if file_path.suffix in {'.conf', '.nginx'} or 'nginx' in path_str:
            return 'Nginx / Proxy'

        if name == 'Makefile':
            return 'Otros CI/CD'

        return 'Otros'

    # ─── LECTURA ─────────────────────────────────────────────────

    def _read_file(self, file_path: Path) -> str:
        for enc in ('utf-8', 'latin-1'):
            try:
                return file_path.read_text(encoding=enc)
            except (UnicodeDecodeError, PermissionError):
                continue
        return "[Error: no se pudo leer el fichero]"

    def _mask_env(self, raw: str) -> str:
        """Oculta los valores de variables de entorno."""
        lines = []
        for line in raw.splitlines():
            stripped = line.strip()
            if '=' in stripped and not stripped.startswith('#') and not stripped.startswith('//'):
                key = stripped.split('=', 1)[0]
                lines.append(f"{key}=***")
            else:
                lines.append(line)
        return '\n'.join(lines)

    # ─── RECOPILACIÓN DE FICHEROS ────────────────────────────────

    def _collect_files(self) -> dict:
        """Devuelve {categoria: [(path, content), ...]}"""
        by_cat: dict = {}

        def add(fp, content):
            cat = self._categorize(fp)
            by_cat.setdefault(cat, []).append((fp, content))

        # 1. Ficheros en la raíz
        print("  📂 Escaneando raíz del proyecto...")
        for item in sorted(self.project_path.iterdir()):
            if item.is_file() and self._include_root_file(item.name):
                content = self._read_file(item)
                if MASK_ENV_VALUES and item.name.startswith('.env'):
                    content = self._mask_env(content)
                add(item, content)
                print(f"     ✓ {item.name}")

        # 2. Ficheros dentro de INCLUDE_DIRS
        for dir_name in INCLUDE_DIRS:
            dir_path = self.project_path / dir_name
            if not dir_path.exists():
                continue
            print(f"  📂 Escaneando {dir_name}/...")
            for root, dirs, files in os.walk(dir_path):
                root_path = Path(root)
                # Excluir subdirectorios prohibidos
                dirs[:] = [d for d in dirs if not self._is_excluded_dir(d)]

                for fname in sorted(files):
                    fp = root_path / fname
                    if self._include_subdir_file(fp):
                        content = self._read_file(fp)
                        if MASK_ENV_VALUES and fname.startswith('.env'):
                            content = self._mask_env(content)
                        add(fp, content)
                        rel = fp.relative_to(self.project_path)
                        print(f"     ✓ {rel}")

        return by_cat

    # ─── HELPERS DE FORMATO ──────────────────────────────────────

    def _lang(self, file_path: Path) -> str:
        name = file_path.name
        if name.startswith('Dockerfile'):
            return 'dockerfile'
        if name.startswith('.env') or name in {'inventory', 'hosts'}:
            return 'dotenv'
        if name == 'Jenkinsfile':
            return 'groovy'
        if name == 'Makefile':
            return 'makefile'
        return LANG_MAP.get(file_path.suffix.lower(), '')

    def _rel(self, file_path: Path) -> str:
        try:
            return str(file_path.relative_to(self.project_path))
        except ValueError:
            return file_path.name

    # ─── SECCIÓN POR FICHERO ─────────────────────────────────────

    def _file_section(self, file_path: Path, content: str) -> str:
        rel    = self._rel(file_path)
        lang   = self._lang(file_path)
        lines  = content.count('\n') + 1
        size   = file_path.stat().st_size if file_path.exists() else 0
        size_h = f"{size:,} bytes" if size < 1024 else f"{size/1024:.1f} KB"

        section  = f"\n## 📄 `{rel}`\n\n"
        section += f"| Campo | Valor |\n"
        section += f"|---|---|\n"
        section += f"| **Ruta completa** | `{file_path}` |\n"
        section += f"| **Nombre** | `{file_path.name}` |\n"
        section += f"| **Extensión** | `{file_path.suffix or '(sin extensión)'}` |\n"
        section += f"| **Tamaño** | {size_h} |\n"
        section += f"| **Líneas** | {lines} |\n\n"
        section += f"```{lang}\n{content}\n```\n\n"
        section += "---\n"
        return section

    # ─── ÁRBOL DE FICHEROS DOCUMENTADOS ──────────────────────────

    def _tree(self, by_cat: dict) -> str:
        """Construye un árbol ASCII real con directorios intermedios."""
        rel_paths = sorted(
            self._rel(fp) for files in by_cat.values() for fp, _ in files
        )
        # Árbol anidado: directorio → dict hijos | None (fichero)
        tree_dict: dict = {}
        for rp in rel_paths:
            parts = Path(rp).parts
            node = tree_dict
            for part in parts[:-1]:
                node = node.setdefault(part, {})
            node[parts[-1]] = None

        def render(node: dict, prefix: str = '') -> list:
            lines = []
            entries = sorted(node.keys(), key=lambda k: (node[k] is None, k.lower()))
            for i, name in enumerate(entries):
                is_last   = (i == len(entries) - 1)
                connector = '└── ' if is_last else '├── '
                child     = node[name]
                if child is None:
                    lines.append(f"{prefix}{connector}{name}")
                else:
                    lines.append(f"{prefix}{connector}{name}/")
                    extension = '    ' if is_last else '│   '
                    lines.extend(render(child, prefix + extension))
            return lines

        result = [f"{self.project_path.name}/"]
        result.extend(render(tree_dict))
        return '\n'.join(result)

    # ─── DOCUMENTOS ──────────────────────────────────────────────

    def _write(self, filename: str, content: str):
        path = self.output_dir / filename
        path.write_text(content, encoding='utf-8')
        print(f"   ✅ {path}")

    def _gen_index(self, by_cat: dict, total: int):
        total_cats = len(by_cat)
        cat_lines  = ''
        for idx, cat in enumerate(CATEGORY_ORDER, 1):
            if cat not in by_cat:
                continue
            n        = len(by_cat[cat])
            slug     = cat.replace('/', '_').replace(' ', '_')
            cat_lines += f"{idx}. **{cat}** — {n} fichero(s) → `{idx:02d}_{slug}.md`\n"

        doc = f"""# 🚀 Documentación DevOps / CI·CD — {self.project_path.name}

**Generado:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
**Proyecto:** `{self.project_path}`
**Ficheros documentados:** {total}
**Categorías:** {total_cats}

---

## 📑 Índice de categorías

{cat_lines}
- **Documento consolidado** → `99_COMPLETO.md`

---

## 🗂️ Árbol de ficheros documentados

```
{self._tree(by_cat)}
```

---

## 💡 Uso sugerido

Sube los `.md` generados a tu IA favorita para:
- Revisar y validar tus pipelines y configuraciones
- Detectar problemas de seguridad en `.env` o IAM
- Documentar la arquitectura de despliegue
- Optimizar Dockerfiles y compose files
- Generar README de infraestructura

---
_Generado con `generate_docs_devops.py`_
"""
        self._write("00_INDEX.md", doc)

    def _gen_category(self, category: str, files: list, idx: int):
        slug = category.replace('/', '_').replace(' ', '_')
        filename = f"{idx:02d}_{slug}.md"

        # Agrupar ficheros por su carpeta padre relativa al proyecto
        groups: dict = {}
        for fp, ct in sorted(files, key=lambda x: self._rel(x[0])):
            rel   = self._rel(fp)
            parts = Path(rel).parts
            # Directorio padre = todo menos el nombre del fichero
            folder = str(Path(*parts[:-1])) if len(parts) > 1 else '(raíz del proyecto)'
            groups.setdefault(folder, []).append((fp, ct))

        # Construir árbol mini de la categoría
        cat_paths = sorted(self._rel(fp) for fp, _ in files)
        cat_tree_dict: dict = {}
        for rp in cat_paths:
            parts = Path(rp).parts
            node = cat_tree_dict
            for part in parts[:-1]:
                node = node.setdefault(part, {})
            node[parts[-1]] = None

        def render_mini(node: dict, prefix: str = '') -> list:
            lines = []
            entries = sorted(node.keys(), key=lambda k: (node[k] is None, k.lower()))
            for i, name in enumerate(entries):
                is_last   = (i == len(entries) - 1)
                connector = '└── ' if is_last else '├── '
                child     = node[name]
                if child is None:
                    lines.append(f"{prefix}{connector}{name}")
                else:
                    lines.append(f"{prefix}{connector}{name}/")
                    extension = '    ' if is_last else '│   '
                    lines.extend(render_mini(child, prefix + extension))
            return lines

        mini_tree_lines = render_mini(cat_tree_dict)
        mini_tree = '\n'.join(mini_tree_lines)

        # Encabezado con árbol
        doc = f"""# {category}

**Total de ficheros:** {len(files)}
**Generado:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

---

## 🗂️ Estructura de esta categoría

```
{mini_tree}
```

---

"""
        # Secciones agrupadas por carpeta
        for folder in sorted(groups.keys()):
            folder_files = groups[folder]
            doc += f"## 📁 `{folder}`\n\n"
            doc += f"_{len(folder_files)} fichero(s) en esta carpeta_\n\n"
            doc += '---\n'
            for fp, ct in folder_files:
                doc += self._file_section(fp, ct)

        self._write(filename, doc)

    def _gen_complete(self, by_cat: dict):
        parts = [f"""# 📦 Proyecto Completo DevOps — {self.project_path.name}

**Generado:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}

Este fichero contiene TODOS los ficheros DevOps del proyecto organizados por categoría.

---
"""]
        for cat in CATEGORY_ORDER:
            if cat not in by_cat:
                continue
            parts.append(f"\n# 📁 {cat}\n")
            for fp, content in sorted(by_cat[cat]):
                parts.append(self._file_section(fp, content))

        self._write("99_COMPLETO.md", ''.join(parts))

    # ─── PUNTO DE ENTRADA ────────────────────────────────────────

    def generate(self):
        if not self.project_path.exists():
            print(f"\n❌ Ruta no encontrada: {self.project_path}")
            return

        print(f"\n{'='*60}")
        print(f"  Proyecto : {self.project_path.name}")
        print(f"  Salida   : {self.output_dir}/")
        print(f"{'='*60}\n")

        print("🔍 Recopilando ficheros DevOps...\n")
        by_cat = self._collect_files()
        total  = sum(len(v) for v in by_cat.values())

        print(f"\n📊 {total} ficheros en {len(by_cat)} categorías\n")
        print("📝 Generando documentos...\n")

        self._gen_index(by_cat, total)

        cat_idx = 1
        for cat in CATEGORY_ORDER:
            if cat not in by_cat:
                continue
            self._gen_category(cat, by_cat[cat], cat_idx)
            cat_idx += 1

        # Categorías no contempladas en CATEGORY_ORDER
        for cat, files in by_cat.items():
            if cat not in CATEGORY_ORDER:
                self._gen_category(cat, files, cat_idx)
                cat_idx += 1

        self._gen_complete(by_cat)

        print(f"\n{'='*60}")
        print(f"🎉 ¡Documentación generada en: {self.output_dir}/")
        print(f"   Ficheros procesados : {total}")
        print(f"   Documentos .md      : {cat_idx + 1}")
        print(f"{'='*60}\n")


# ================================================================
if __name__ == "__main__":
    DevOpsDocGenerator(
        project_path=PROJECT_PATH,
        output_dir=OUTPUT_DIR,
    ).generate()