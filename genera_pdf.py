import os
import time
from fpdf import FPDF
from datetime import datetime

# Limpiar el archivo prompt
with open("prompt.txt", "w", encoding='utf-8') as archivo_prompt:
    archivo_prompt.write(" " + "\n")

# Definir las extensiones de los archivos a validar
extensiones_validas = ['php', 'html', 'js', 'css']

# Obtener la ruta del directorio que se desea recorrer
ruta_directorio = "D:/riego/ayun/"

# Verificar si la ruta ingresada es válida y corresponde a un directorio
if not os.path.isdir(ruta_directorio):
    print("La ruta ingresada no es válida o no corresponde a un directorio.")
else:
    # Iniciar el prompt de validación
    mensaje_prompt = f"Pon foco en no responder hasta que leas <fin del contexto>. Bienvenido a mi proyecto de monitoreo ambiental para plantas. El objetivo de este proyecto es monitorear y controlar las condiciones ambientales de mis plantas para asegurar su crecimiento y salud óptimos. Para lograr esto, he creado un sistema que utiliza sensores conectados a un Arduino para medir la humedad del suelo y la temperatura del aire, y una aplicación web en PHP, HTML, JS y CSS que muestra los datos en tiempo real y proporciona información sobre el estado de las plantas. También estoy trabajando en un módulo de riego automático que se activará cuando los sensores detecten que la humedad del suelo es baja. A continuación, encontrarás el código que he escrito hasta ahora para el sistema de monitoreo ambiental. Espero que lo encuentres útil y que me puedas proporcionar retroalimentación para mejorarlo aún más. Te iré enviando distintos prompt con el contexto completo, no me respondas hasta que finalice con <fin del contexto>\n"
    mensaje_prompt += f"==============================\n\n"

    # Recorrer el directorio y validar los archivos con las extensiones definidas
    for ruta_actual, carpetas, archivos in os.walk(ruta_directorio):
        for archivo in archivos:
            nombre_archivo, extension_archivo = os.path.splitext(archivo)
            if extension_archivo[1:] in extensiones_validas:
                ruta_archivo = os.path.join(ruta_actual, archivo)
                mensaje_prompt += f"Archivo: {archivo}\n"
                mensaje_prompt += f"Ruta: {ruta_archivo}\n"
                with open(ruta_archivo, 'r', encoding='utf-8') as f:
                    codigo = f.read()
                    codigo_con_espacios = "\n".join(["        " + line for line in codigo.splitlines()])
                    mensaje_prompt += f"Código:\n    [fin_codigo]\n{codigo_con_espacios}\n    [fin_codigo]\n"
    mensaje_prompt+="\n <fin del contexto> De ahora en adelante te haré preguntas y debes utilizar el contexto anterior"
    with open("text.txt", "w", encoding='utf-8') as archivo_text:
        archivo_text.write(mensaje_prompt)

# Obtener la fecha y hora actual
fecha_hora = datetime.now().strftime("%Y-%m-%d %H%M%S")

# Crear un nuevo archivo PDF con el nombre deseado
pdf = FPDF()

# Añadir una página
pdf.add_page()

# Establecer la fuente
pdf.set_font("Arial", size = 12)


# Leer el contenido del archivo text.txt
with open("text.txt", "r", encoding='utf-8') as archivo_text:
    contenido = archivo_text.read()

# Obtener la fecha y hora actual
fecha_hora = datetime.now().strftime("%Y-%m-%d %H%M%S")

class PDF(FPDF):
    def header(self):
        self.set_font('Arial', 'B', 12)

    def chapter_title(self, title):
        self.set_font('Arial', 'B', 12)
        self.cell(0, 10, 'Capítulo: %s' % title, 0, 1, 'C')
        self.ln(10)

    def chapter_body(self, body):
        self.set_font('Arial', '', 8)
        self.multi_cell(0, 4, body)
        self.ln()

    def add_chapter(self, title, body):
        self.add_page()
        self.chapter_title(title)
        self.chapter_body(body)


pdf = PDF()
pdf.set_left_margin(10)
pdf.set_right_margin(10)
pdf.set_auto_page_break(auto = True, margin = 15)

# Agregar el contenido del archivo al PDF
pdf.add_chapter('Proyecto Ayun', contenido)

# Guardar el archivo PDF
pdf.output(f"proyecto_ayun_{fecha_hora}.pdf")
