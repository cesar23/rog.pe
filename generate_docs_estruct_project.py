import os
import json
import fnmatch
import xml.dom.minidom
from pathlib import Path
from datetime import datetime


# ================================================================
#  CONFIGURACIÓN — edita solo esta sección
# ================================================================

# Ruta al repositorio/proyecto que quieres documentar
# Windows : r"C:\Users\tu_usuario\proyectos\mi-proyecto"
# Linux   : "/home/usuario/proyectos/mi-proyecto"
# Relativa: "../mi-proyecto"
PROJECT_PATH = "."

OUTPUT_DIR = "project_structure_docs"

# ── Extensiones excluidas del árbol ─────────────────────────────
# Añade o elimina extensiones según tu proyecto
EXCLUDE_EXTENSIONS = {
    # Imágenes
    '.png', '.jpg', '.jpeg', '.gif', '.svg', '.ico', '.webp',
    '.bmp', '.tiff', '.psd', '.ai', '.eps',
    # Fuentes
    '.woff', '.woff2', '.ttf', '.eot', '.otf',
    # Vídeo / Audio
    '.mp4', '.mp3', '.wav', '.avi', '.mov', '.ogg', '.flac', '.webm',
    # Texto plano / logs / datos
    '.txt', '.log', '.csv',
    # Comprimidos
    '.zip', '.rar', '.iso', '.tar', '.gz', '.7z', '.bz2',
    # Documentación binaria
    '.md', '.pdf', '.doc', '.docx', '.xls', '.xlsx',
    # Otros binarios
    '.exe', '.dll', '.so', '.bin', '.dat',
}

# ================================================================
#  LISTA BLANCA DE DIRECTORIOS — tienen prioridad sobre CUALQUIER exclusión
# ================================================================
# Agrega aquí los directorios que NUNCA deben ser excluidos,
# aunque empiecen por "." o estén en EXCLUDE_DIRS.
#
# ── Docker ──────────────────────────────────────────────────────
#   '.docker'          → configuración Docker personalizada
#   '.devcontainer'    → contenedores de desarrollo (VS Code / GitHub Codespaces)
#
# ── Control de versiones / IDE ───────────────────────────────────
#   '.github'          → workflows GitHub Actions, PR templates, etc.
#   '.husky'           → hooks de Git gestionados por Husky
#   '.gitlab'          → configuración específica de GitLab
#
# ── CI/CD y orquestación ────────────────────────────────────────
#   '.circleci'        → pipelines de CircleCI
#   '.azure-pipelines' → pipelines de Azure DevOps
#   '.teamcity'        → configuración de TeamCity
#
# ── Infraestructura como código ─────────────────────────────────
#   'terraform'        → módulos e infraestructura Terraform
#   'ansible'          → playbooks y roles de Ansible
#   'k8s'             → manifiestos Kubernetes (alternativa a "kubernetes")
#   'kubernetes'       → manifiestos Kubernetes
#   'helm'             → charts de Helm
#   'charts'           → charts de Helm (convención alternativa)
#   'infra'            → carpeta genérica de infraestructura
#   'deploy'           → scripts y configs de despliegue
#   'ops'              → carpeta genérica DevOps/Ops
#   'scripts'          → scripts de automatización y CI
#   'envs'             → configuraciones por entorno (dev/staging/prod)
#   'environments'     → ídem, convención alternativa
FORCE_INCLUDE_DIRS = {
    # Docker
    '.docker',
    '.devcontainer',
    # Git / IDE
    '.github',
    '.gitlab',
    '.husky',
    # CI/CD
    '.circleci',
    '.azure-pipelines',
    '.teamcity',
    # IaC / orquestación
    'terraform',
    'ansible',
    'k8s',
    'kubernetes',
    'helm',
    'charts',
    # Despliegue / Ops
    'infra',
    'deploy',
    'ops',
    'scripts',
    'envs',
    'environments',
}

# ── Directorios excluidos completamente (soporta wildcards) ─────
# Estas carpetas NO aparecen en el árbol ni se escanea su contenido
# Ejemplos de wildcards: 'test_*', '*_backup', 'cache?'
EXCLUDE_DIRS = {
    '.myScriptRepo',          # ← coma corregida (antes faltaba y se concatenaba con 'node_modules')
    'node_modules', 'vendor', '.git', '.idea', '.vscode',
    'dist', 'build', '__pycache__', '.cache', 'coverage',
    '.next', '.nuxt', '.svelte-kit', 'out', '.turbo', 'tmp', 'temp',
    'www','project_structure_docs','Resources',
}

# ── Directorios que SE MUESTRAN en el árbol pero su contenido
# se omite completamente (soporta wildcards) ──────────────────────
# La carpeta aparece con la etiqueta [contenido omitido]
# Ejemplo: 'wp-*' cubre wp-admin, wp-includes, etc.
COLLAPSE_DIRS = {
    'my_resource', 'Resources', 'public_html',
}

# ── Subcarpetas a colapsar según su carpeta padre ────────────────
# La carpeta padre SE MUESTRA con su contenido de primer nivel,
# pero cada subcarpeta hija que coincida aparece con [contenido omitido].
#
# Formato:
#   'nombre_padre': {'patron1', 'patron2', ...}
#
# Soporta wildcards en los patrones de hijos:
#   {'*'}                 → colapsa TODAS las subcarpetas del padre
#   {'uploads', 'cache*'} → colapsa solo las que coincidan
#
# Ejemplo real WordPress:
#   'wp-content': {'*'}
#       → muestra wp-content/ con plugins/, themes/, uploads/...
#         pero cada una aparece como [contenido omitido]
COLLAPSE_CHILDREN = {
    'wp-content': {'*'},
}

# ── Ficheros excluidos por nombre exacto ────────────────────────
EXCLUDE_FILES = {
    '.DS_Store', 'Thumbs.db', 'desktop.ini',
    'package-lock.json', 'yarn.lock', 'composer.lock',
}

# ── Ficheros cuyo CONTENIDO se incluye en el reporte ────────────
# Clave  : nombre exacto del fichero
# Valor  : etiqueta descriptiva que aparecerá en el Markdown
CONFIG_FILES_TO_SHOW = {
    # ── Docker ──────────────────────────────────────────────────
    'Dockerfile':                  'Docker — Imagen principal',
    'Dockerfile.dev':              'Docker — Imagen desarrollo',
    'Dockerfile.prod':             'Docker — Imagen producción',
    'docker-compose.yml':          'Docker Compose',
    'docker-compose.yaml':         'Docker Compose',
    'docker-compose.dev.yml':      'Docker Compose (desarrollo)',
    'docker-compose.dev.yaml':     'Docker Compose (desarrollo)',
    'docker-compose.prod.yml':     'Docker Compose (producción)',
    'docker-compose.prod.yaml':    'Docker Compose (producción)',
    'docker-compose.staging.yml':  'Docker Compose (staging)',
    'docker-compose.staging.yaml': 'Docker Compose (staging)',
    'docker-compose.override.yml': 'Docker Compose (override)',
    '.dockerignore':               'Docker — Ignore file',

    # ── CI/CD — GitHub Actions ───────────────────────────────────
    # (los workflows están en .github/workflows/*.yml — se leen al escanear el árbol)

    # ── CI/CD — GitLab CI ────────────────────────────────────────
    '.gitlab-ci.yml':              'GitLab CI/CD Pipeline',
    '.gitlab-ci.yaml':             'GitLab CI/CD Pipeline',

    # ── CI/CD — Jenkins ─────────────────────────────────────────
    'Jenkinsfile':                 'Jenkins Pipeline',

    # ── CI/CD — CircleCI ────────────────────────────────────────
    '.circleci/config.yml':        'CircleCI Config',

    # ── CI/CD — Azure DevOps ────────────────────────────────────
    'azure-pipelines.yml':         'Azure DevOps Pipeline',
    'azure-pipelines.yaml':        'Azure DevOps Pipeline',

    # ── CI/CD — Bitbucket ───────────────────────────────────────
    'bitbucket-pipelines.yml':     'Bitbucket Pipelines',

    # ── CI/CD — Travis / otros ──────────────────────────────────
    '.travis.yml':                 'Travis CI Config',
    'Makefile':                    'Makefile (automatización)',

    # ── Infraestructura como código — Terraform ──────────────────
    'main.tf':                     'Terraform — Main',
    'variables.tf':                'Terraform — Variables',
    'outputs.tf':                  'Terraform — Outputs',
    'providers.tf':                'Terraform — Providers',
    'backend.tf':                  'Terraform — Backend',
    'terraform.tfvars':            'Terraform — Valores de variables',
    '.terraform-version':          'Terraform — Versión requerida',

    # ── Infraestructura como código — Ansible ────────────────────
    'ansible.cfg':                 'Ansible — Configuración',
    'playbook.yml':                'Ansible — Playbook principal',
    'playbook.yaml':               'Ansible — Playbook principal',
    'inventory':                   'Ansible — Inventario',
    'hosts':                       'Ansible — Hosts',
    'requirements.yml':            'Ansible — Galaxy Requirements',

    # ── Kubernetes ───────────────────────────────────────────────
    'skaffold.yaml':               'Skaffold (dev en K8s)',
    'skaffold.yml':                'Skaffold (dev en K8s)',
    'kustomization.yaml':          'Kustomize — Overlay config',
    'kustomization.yml':           'Kustomize — Overlay config',

    # ── PHP / JS ─────────────────────────────────────────────────
    'composer.json':               'Composer (PHP dependencies)',
    'package.json':                'NPM Package',
    'webpack.config.js':           'Webpack Config',
    'vite.config.js':              'Vite Config',
    'vite.config.ts':              'Vite Config (TS)',
    'tsconfig.json':               'TypeScript Config',
    '.eslintrc.js':                'ESLint Config',
    '.eslintrc.json':              'ESLint Config',

    # ── Java ─────────────────────────────────────────────────────
    'pom.xml':                     'Maven POM',
    'application.yml':             'Spring Application Config',
    'application.yaml':            'Spring Application Config',

    # ── Variables de entorno ─────────────────────────────────────
    '.env':                        'Environment Variables',
    '.env.example':                'Environment Variables (example)',
    '.env.local':                  'Environment Variables (local)',
    '.env.production':             'Environment Variables (production)',
    '.env.development':            'Environment Variables (development)',
    '.env.staging':                'Environment Variables (staging)',
}

# ================================================================
#  GENERADOR — no es necesario editar más abajo
# ================================================================

class ProjectStructureGenerator:

    def __init__(self):
        self.project_path = Path(PROJECT_PATH)
        self.output_dir   = Path(OUTPUT_DIR)
        self.output_dir.mkdir(exist_ok=True)

    # ─── HELPERS DE COINCIDENCIA CON WILDCARDS ───────────────────

    def _matches_any(self, name: str, patterns: set) -> bool:
        """Devuelve True si 'name' coincide con algún patrón del set (soporta wildcards)."""
        for pattern in patterns:
            if fnmatch.fnmatch(name, pattern):
                return True
        return False

    # ─── FILTROS ─────────────────────────────────────────────────

    def _exclude_dir(self, name: str) -> bool:
        """True → la carpeta se omite por completo del árbol y del escaneo.

        FORCE_INCLUDE_DIRS tiene prioridad absoluta: si el directorio
        está ahí, NUNCA se excluye aunque empiece por '.' o esté en EXCLUDE_DIRS.
        """
        # 🔒 Lista blanca — prioridad máxima
        if name in FORCE_INCLUDE_DIRS:
            return False

        # Excluir por nombre / patrón explícito
        if self._matches_any(name, EXCLUDE_DIRS):
            return True

        # Excluir carpetas ocultas no contempladas en la lista blanca
        if name.startswith('.'):
            return True

        return False

    def _collapse_dir(self, name: str) -> bool:
        """True → la carpeta aparece en el árbol pero su contenido se omite por completo."""
        return self._matches_any(name, COLLAPSE_DIRS)

    def _collapse_by_parent(self, name: str, parent: str) -> bool:
        """True → la carpeta se colapsa porque su padre está en COLLAPSE_CHILDREN."""
        if parent not in COLLAPSE_CHILDREN:
            return False
        return self._matches_any(name, COLLAPSE_CHILDREN[parent])

    def _exclude_file(self, name: str) -> bool:
        if name in EXCLUDE_FILES:
            return True
        ext = Path(name).suffix.lower()
        if ext in EXCLUDE_EXTENSIONS:
            return True
        # Excluir ficheros ocultos que no sean de configuración conocida
        if name.startswith('.') and name not in CONFIG_FILES_TO_SHOW:
            return True
        return False

    # ─── ÁRBOL ASCII ─────────────────────────────────────────────

    def _build_tree(self, path: Path, prefix: str = "", is_last: bool = True, parent: str = "") -> list:
        lines = []
        connector = "└── " if is_last else "├── "

        # Carpeta colapsada por COLLAPSE_DIRS o por ser hija de COLLAPSE_CHILDREN
        if self._collapse_dir(path.name) or self._collapse_by_parent(path.name, parent):
            lines.append(f"{prefix}{connector}{path.name}/  [contenido omitido]")
            return lines

        lines.append(f"{prefix}{connector}{path.name}/")
        child_prefix = prefix + ("    " if is_last else "│   ")

        try:
            entries = sorted(path.iterdir(), key=lambda p: (p.is_file(), p.name.lower()))
        except PermissionError:
            lines.append(f"{child_prefix}└── [sin permiso de lectura]")
            return lines

        dirs  = [e for e in entries if e.is_dir()  and not self._exclude_dir(e.name)]
        files = [e for e in entries if e.is_file() and not self._exclude_file(e.name)]
        children = dirs + files

        for i, child in enumerate(children):
            last = (i == len(children) - 1)
            if child.is_dir():
                lines.extend(self._build_tree(child, child_prefix, last, parent=path.name))
            else:
                c = "└── " if last else "├── "
                lines.append(f"{child_prefix}{c}{child.name}")

        return lines

    def _tree_string(self) -> str:
        lines = [f"{self.project_path.name}/"]
        try:
            entries = sorted(self.project_path.iterdir(),
                             key=lambda p: (p.is_file(), p.name.lower()))
        except PermissionError:
            return "[sin permiso de lectura]"

        dirs  = [e for e in entries if e.is_dir()  and not self._exclude_dir(e.name)]
        files = [e for e in entries if e.is_file() and not self._exclude_file(e.name)]
        children = dirs + files

        for i, child in enumerate(children):
            last = (i == len(children) - 1)
            c = "└── " if last else "├── "
            if child.is_dir():
                if self._collapse_dir(child.name):
                    lines.append(f"{c}{child.name}/  [contenido omitido]")
                else:
                    lines.extend(self._build_tree(child, "", last, parent=""))
            else:
                lines.append(f"{c}{child.name}")

        return "\n".join(lines)

    # ─── ESTADÍSTICAS ────────────────────────────────────────────

    def _collect_stats(self) -> dict:
        stats = {
            "total_dirs":     0,
            "total_files":    0,
            "excluded_dirs":  [],
            "collapsed_dirs": [],
            "extensions":     {},
        }

        for root, dirs, files in os.walk(self.project_path):
            root_path   = Path(root)
            parent_name = root_path.name

            for d in dirs:
                if self._exclude_dir(d):
                    rel = (root_path / d).relative_to(self.project_path)
                    stats["excluded_dirs"].append(str(rel))
                elif self._collapse_dir(d) or self._collapse_by_parent(d, parent_name):
                    rel = (root_path / d).relative_to(self.project_path)
                    stats["collapsed_dirs"].append(str(rel))

            # No descender en excluidos ni en colapsados
            dirs[:] = [
                d for d in dirs
                if not self._exclude_dir(d)
                and not self._collapse_dir(d)
                and not self._collapse_by_parent(d, parent_name)
            ]
            stats["total_dirs"] += len(dirs)

            for f in files:
                if not self._exclude_file(f):
                    stats["total_files"] += 1
                    ext = Path(f).suffix.lower() or "(sin extensión)"
                    stats["extensions"][ext] = stats["extensions"].get(ext, 0) + 1

        return stats

    # ─── LECTURA DE FICHEROS DE CONFIGURACIÓN ────────────────────

    def _read_config_file(self, file_path: Path) -> str:
        """Lee el fichero y lo formatea según su tipo."""
        try:
            raw = file_path.read_text(encoding="utf-8")
        except Exception as e:
            return f"[Error al leer: {e}]"

        ext  = file_path.suffix.lower()
        name = file_path.name

        # JSON → pretty print
        if ext == ".json":
            try:
                return json.dumps(json.loads(raw), indent=2, ensure_ascii=False)
            except Exception:
                return raw

        # XML (pom.xml) → pretty print
        if ext == ".xml":
            try:
                dom = xml.dom.minidom.parseString(raw.encode("utf-8"))
                return dom.toprettyxml(indent="  ")
            except Exception:
                return raw

        # .env y variantes → ocultar valores sensibles
        if name.startswith(".env"):
            lines_out = []
            for line in raw.splitlines():
                stripped = line.strip()
                if "=" in stripped and not stripped.startswith("#"):
                    key = stripped.split("=", 1)[0]
                    lines_out.append(f"{key}=***")
                else:
                    lines_out.append(line)
            return "\n".join(lines_out)

        # Resto → texto plano tal cual
        return raw

    def _lang_for(self, file_path: Path) -> str:
        ext_map = {
            ".json":  "json",
            ".js":    "javascript",
            ".ts":    "typescript",
            ".xml":   "xml",
            ".yml":   "yaml",
            ".yaml":  "yaml",
            ".env":   "dotenv",
            ".tf":    "hcl",
            ".tfvars":"hcl",
            ".sh":    "bash",
            ".cfg":   "ini",
        }
        name = file_path.name
        if name.startswith(".env"):
            return "dotenv"
        if name.startswith("Dockerfile"):
            return "dockerfile"
        if name in {"Jenkinsfile", "Makefile", "inventory", "hosts", "playbook.yml", "playbook.yaml", "ansible.cfg"}:
            return {"Jenkinsfile": "groovy", "Makefile": "makefile",
                    "inventory": "ini", "hosts": "ini",
                    "playbook.yml": "yaml", "playbook.yaml": "yaml",
                    "ansible.cfg": "ini"}.get(name, "")
        return ext_map.get(file_path.suffix.lower(), "")

    def _config_files_section(self) -> str:
        found = []
        for filename, label in CONFIG_FILES_TO_SHOW.items():
            fpath = self.project_path / filename
            if not fpath.exists():
                continue
            content = self._read_config_file(fpath)
            lang    = self._lang_for(fpath)
            found.append(
                f"### `{filename}` — _{label}_\n\n"
                f"```{lang}\n{content}\n```\n"
            )

        if not found:
            return (
                "\n## Ficheros de configuración\n\n"
                "_No se encontró ningún fichero de configuración conocido en la raíz._\n\n---\n"
            )

        body = "\n---\n\n".join(found)
        return f"\n## Ficheros de configuración\n\n{body}\n---\n"

    # ─── MARKDOWN FINAL ──────────────────────────────────────────

    def _build_markdown(self) -> str:
        print("  Generando árbol...")
        tree           = self._tree_string()
        print("  Recopilando estadísticas...")
        stats          = self._collect_stats()
        print("  Leyendo ficheros de configuración...")
        config_section = self._config_files_section()

        ext_rows = "\n".join(
            f"| `{ext}` | {count} |"
            for ext, count in sorted(stats["extensions"].items(), key=lambda x: -x[1])
        )

        excluded_list = "\n".join(
            f"- `{d}`" for d in sorted(stats["excluded_dirs"])
        ) or "_Ninguno detectado_"

        collapsed_list = "\n".join(
            f"- `{d}`" for d in sorted(stats["collapsed_dirs"])
        ) or "_Ninguno detectado_"

        collapse_children_note = "\n".join(
            f"- `{parent}/` → hijos colapsados con patrón: "
            + ", ".join(f"`{p}`" for p in sorted(patterns))
            for parent, patterns in COLLAPSE_CHILDREN.items()
        ) or "_Ninguno configurado_"

        force_include_note = ", ".join(f"`{d}`" for d in sorted(FORCE_INCLUDE_DIRS))

        return f"""# Estructura del Proyecto: {self.project_path.name}

**Ruta:** `{self.project_path.resolve()}`
**Generado:** {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}
**Directorios escaneados:** {stats['total_dirs']}
**Ficheros incluidos:** {stats['total_files']}

> **Directorios forzados a incluir (lista blanca):** {force_include_note}
> **Directorios omitidos completamente:** `node_modules`, `vendor`, `.git`, `dist`, `build`...
> **Directorios colapsados** (visibles pero sin contenido): `my_resource`...
> **Subcarpetas colapsadas por padre:** {collapse_children_note}
> **Extensiones omitidas:** imágenes, fuentes, vídeo, audio, `.txt`, `.log`, `.csv`, `.zip`, `.rar`, `.iso`, `.md`...

---

## Árbol de directorios y ficheros

```
{tree}
```

---
{config_section}
## Resumen de extensiones presentes

| Extensión | Ficheros |
|-----------|:--------:|
{ext_rows}

---

## Directorios excluidos completamente

{excluded_list}

---

## Directorios colapsados (contenido omitido)

{collapsed_list}

---

_Generado con `generate_docs_estruct_project.py`_
"""

    # ─── PUNTO DE ENTRADA ────────────────────────────────────────

    def generate(self):
        if not self.project_path.exists():
            print(f"\nERROR: Ruta no encontrada → {self.project_path.resolve()}")
            return

        print(f"\nProyecto : {self.project_path.name}")
        print(f"Salida   : {self.output_dir}/\n")

        content     = self._build_markdown()
        output_file = self.output_dir / f"{self.project_path.name}_structure.md"
        output_file.write_text(content, encoding="utf-8")

        print(f"\n✓ Fichero generado : {output_file}")
        print(f"  Líneas totales   : {content.count(chr(10)) + 1}")


# ================================================================
if __name__ == "__main__":
    ProjectStructureGenerator().generate()

    print("\n" + "=" * 60)
    print("¡Proceso completado!")
    print(f"El fichero .md está en: {OUTPUT_DIR}/")
    print("=" * 60)